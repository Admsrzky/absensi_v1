<?php

namespace App\Http\Controllers;

use App\Models\Absen; // Assuming your model is named Absen
use App\Models\Pengajuan_Izin;
use App\Models\Setting; // Ensure this is imported
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
// use Telegram\Bot\Laravel\Facades\Telegram; // Uncomment if you are actively using this facade
// use App\Http\Controllers\TelegramController; // Uncomment if this exists and has sendMessage method

class AbsensiController extends Controller
{

    /**
     * Set the attendance period in session.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setPeriode(Request $request)
    {
        $tanggal = $request->tanggal;
        session(['periode' => $tanggal]);

        return Redirect::back();
    }

    /**
     * Display a listing of the attendance records.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $tanggal = date('Y-m'); // Default to current month-year
        $periode = session('periode');
        $query = Absen::query();

        if ($periode) {
            $query->whereRaw('DATE_FORMAT(tanggal, "%Y-%m") = ?', [$periode]);
        }

        // Mengambil semua data absen tanpa filter jabatan spesifik
        // Jika Anda ingin filter berdasarkan jabatan user yang login, Anda perlu menambahkan kembali logika ini.
        $absen = $query->get();

        // Mengambil jumlah semua pengajuan izin yang belum disetujui
        $jumlahIzin = Pengajuan_Izin::where('status_approved', 0)->count();

        return view('absensi.index', compact('absen', 'jumlahIzin'));
    }

    /**
     * Show the form for creating a new attendance record (camera view).
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $hariini = date("Y-m-d");
        $userId = Auth::id();

        // Fetch attendance settings for display in the frontend
        $settings = Setting::whereIn('key', ['jam_masuk_start', 'jam_masuk_end', 'jam_keluar_min'])
            ->pluck('value', 'key');

        $jamMasukStart = $settings['jam_masuk_start'] ?? '08:00:00';
        $jamMasukEnd = $settings['jam_masuk_end'] ?? '09:00:00';
        $jamKeluarMin = $settings['jam_keluar_min'] ?? '17:00:00'; // Not directly used in create, but good to fetch

        // Check for an existing 'H' (Hadir) entry for today that hasn't been clocked out yet
        $cek = Absen::where('user_id', $userId)
            ->where('tanggal', $hariini)
            ->where('status', 'H')
            ->whereNull('jam_keluar') // Only consider entries where jam_keluar is null
            ->orderBy('id', 'desc')
            ->first();

        $selisihWaktu = 24 * 60; // Default to a large value (in minutes) to indicate no recent clock-in or already clocked out
        if ($cek) {
            $jamMasuk = Carbon::parse($cek->jam_masuk);
            $selisihWaktu = Carbon::now()->diffInMinutes($jamMasuk); // Calculate difference in minutes
        }

        return view('absensi.create', compact('cek', 'hariini', 'selisihWaktu', 'jamMasukStart', 'jamMasukEnd', 'jamKeluarMin'));
    }

    /**
     * Store a newly created attendance record in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|string', // Base64 image string
            'lokasi' => 'required|string', // "latitude,longitude|address"
            'jenis_absen' => 'required|in:masuk,keluar',
        ]);

        $email = Auth::user()->email;
        $nama = Auth::user()->nama;
        $tanggal = date("Y-m-d");
        $jam = date("H:i:s");
        $jenisAbsen = $request->jenis_absen;
        $userId = Auth::id();
        $currentTime = Carbon::now();

        // Ambil pengaturan jam dari database
        $settings = Setting::whereIn('key', ['jam_masuk_start', 'jam_masuk_end', 'jam_keluar_min'])
            ->pluck('value', 'key');

        $jamMasukStart = Carbon::createFromTimeString($settings['jam_masuk_start'] ?? '08:00:00');
        $jamMasukEnd = Carbon::createFromTimeString($settings['jam_masuk_end'] ?? '09:00:00');
        $jamKeluarMin = Carbon::createFromTimeString($settings['jam_keluar_min'] ?? '17:00:00');

        // Parse lokasi string: "latitude,longitude|address"
        list($coords, $address) = explode('|', $request->lokasi);

        $folderPath = "public/uploads/absensi/";
        $formatName = $email . "-" . $tanggal . "-" . $jenisAbsen;
        $image_parts = explode(";base64,", $request->image);
        $image_base64 = base64_decode($image_parts[1]);
        $fileName = $formatName . ".jpeg";
        $filePath = $folderPath . $fileName;

        // Ensure the directory exists
        if (!Storage::exists($folderPath)) {
            Storage::makeDirectory($folderPath);
        }

        if ($jenisAbsen == 'masuk') {
            // Check clock-in time window
            if (!$currentTime->between($jamMasukStart, $jamMasukEnd)) {
                return response('error|Waktu absen masuk adalah jam ' . $jamMasukStart->format('H:i') . ' sampai ' . $jamMasukEnd->format('H:i') . '.|in', 200);
            }

            // Check if user has already clocked in today
            $hasClockedInToday = Absen::where('user_id', $userId)
                ->where('tanggal', $tanggal)
                ->whereNotNull('jam_masuk')
                ->exists();

            if ($hasClockedInToday) {
                return response('error|Anda sudah melakukan absen masuk hari ini.|in', 200);
            }

            $data = [
                'user_id' => $userId,
                'email' => $email,
                'nama' => $nama,
                'status' => 'H', // Hadir
                'keterangan' => '', // Inisialisasi keterangan dengan string kosong
                'tanggal' => $tanggal,
                'tanggal_keluar' => null, // Inisialisasi tanggal_keluar
                'jam_masuk' => $jam,
                'jam_keluar' => null, // Inisialisasi jam_keluar
                'foto_masuk' => $fileName,
                'foto_keluar' => '', // Inisialisasi foto_keluar dengan string kosong
                'lokasi_masuk' => $request->lokasi, // Store full location string
                'lokasi_keluar' => '', // Inisialisasi lokasi_keluar dengan string kosong
                'laporan_masuk' => '', // Menggunakan string kosong
                'laporan_keluar' => '', // Menggunakan string kosong
                'status_validasi' => 0, // Default status validasi
            ];

            try {
                Absen::create($data);
                Storage::put($filePath, $image_base64);
                return response('success|Terimakasih, Selamat bekerja!|in', 200);
            } catch (\Exception $e) {
                return response('error|Maaf, absen masuk tidak berhasil. Error: ' . $e->getMessage() . '|in', 200);
            }
        } else if ($jenisAbsen == 'keluar') {
            // Check clock-out time
            if ($currentTime->lessThan($jamKeluarMin)) {
                return response('error|Waktu absen keluar adalah jam ' . $jamKeluarMin->format('H:i') . ' atau setelahnya.|out', 200);
            }

            // Find the latest open clock-in entry for today
            $latestClockInEntry = Absen::where('user_id', $userId)
                ->where('tanggal', $tanggal)
                ->whereNotNull('jam_masuk')
                ->whereNull('jam_keluar') // Must not have clocked out yet
                ->orderBy('id', 'desc')
                ->first();

            if (!$latestClockInEntry) {
                return response('error|Anda belum absen masuk hari ini atau sudah absen keluar.|out', 200);
            }

            $data_pulang = [
                'jam_keluar' => $jam,
                'foto_keluar' => $fileName,
                'lokasi_keluar' => $request->lokasi, // Store full location string
                'laporan_keluar' => '', // Menggunakan string kosong
            ];

            try {
                $latestClockInEntry->update($data_pulang);
                Storage::put($filePath, $image_base64);
                return response('success|Terimakasih, Selamat beristirahat!|out', 200);
            } catch (\Exception $e) {
                return response('error|Maaf, absen keluar tidak berhasil. Error: ' . $e->getMessage() . '|out', 200);
            }
        }
        return response('error|Jenis absen tidak valid.|unknown', 200);
    }

    /**
     * Display the dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        $userId = Auth::id();
        $today = Carbon::today()->toDateString();

        // Get the latest attendance entry for the current user and today
        $latestEntry = Absen::where('user_id', $userId)
            ->where('tanggal', $today)
            ->orderBy('id', 'desc')
            ->first();

        // Calculate selisihWaktu (time difference from jam_masuk in minutes)
        $selisihWaktu = null;
        if ($latestEntry && $latestEntry->jam_masuk) {
            $jamMasuk = Carbon::parse($latestEntry->jam_masuk);
            $selisihWaktu = Carbon::now()->diffInMinutes($jamMasuk);
        }

        // Calculate selisihWaktuOut (time difference from jam_keluar in minutes)
        $selisihWaktuOut = null;
        if ($latestEntry && $latestEntry->jam_keluar) {
            $jamKeluar = Carbon::parse($latestEntry->jam_keluar);
            $selisihWaktuOut = Carbon::now()->diffInMinutes($jamKeluar);
        }

        // Determine $cek for the bottom menu logic (if user has clocked in but not out)
        $cek = Absen::where('user_id', $userId)
            ->where('tanggal', $today)
            ->whereNotNull('jam_masuk')
            ->whereNull('jam_keluar')
            ->first();

        $bulan = date('m') * 1;
        $tahun = date('Y');
        $namaBulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];

        // Rekap Absen Bulan Ini (Hadir) - Mengambil data semua karyawan
        $rekapAbsen = Absen::selectRaw('COUNT(id) as jumlah_hadir')
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->where('status', 'H')
            ->first();

        // Rekap Izin/Sakit Bulan Ini - Mengambil data semua karyawan
        $rekapIzin = Absen::selectRaw('SUM(CASE WHEN status = "I" THEN 1 ELSE 0 END) as jumlah_izin,
                                        SUM(CASE WHEN status = "S" THEN 1 ELSE 0 END) as jumlah_sakit')
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->first();

        // Total Karyawan (untuk card pertama)
        $user = User::count(); // Mengambil jumlah total user

        // Menghitung jumlah hari dalam bulan ini
        $totalHariDalamBulan = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);

        // Menghitung jumlah hari kerja (total hari - hari Minggu)
        $jumlahHariKerja = 0;
        for ($i = 1; $i <= $totalHariDalamBulan; $i++) {
            $date = Carbon::createFromDate($tahun, $bulan, $i);
            if ($date->dayOfWeek !== Carbon::SUNDAY) { // Carbon::SUNDAY adalah 0
                $jumlahHariKerja++;
            }
        }

        // Menghitung Alpha (Tidak Hadir Tanpa Keterangan)
        // Alpha = (Jumlah Hari Kerja * Total Karyawan) - (Jumlah Hadir + Jumlah Izin + Jumlah Sakit)
        $totalKehadiranTerdata = ($rekapAbsen->jumlah_hadir ?? 0) + ($rekapIzin->jumlah_izin ?? 0) + ($rekapIzin->jumlah_sakit ?? 0);
        $alphaCount = ($jumlahHariKerja * $user) - $totalKehadiranTerdata;
        $alphaCount = max(0, $alphaCount); // Pastikan tidak ada nilai negatif

        // Rekapitulasi Per User untuk Tabel
        $rekapitulasiPerUser = Absen::select(
            'users.nama',
            DB::raw('COUNT(CASE WHEN absens.status = "H" THEN 1 END) as jumlah_hadir'),
            DB::raw('COUNT(CASE WHEN absens.status = "I" THEN 1 END) as jumlah_izin'),
            DB::raw('COUNT(CASE WHEN absens.status = "S" THEN 1 END) as jumlah_sakit'),
            DB::raw('SUM(CASE WHEN absens.status = "H" THEN TIMESTAMPDIFF(SECOND, absens.jam_masuk, absens.jam_keluar) ELSE 0 END) as total_detik_kerja')
        )
            ->join('users', 'absens.user_id', '=', 'users.id')
            ->whereMonth('absens.tanggal', $bulan)
            ->whereYear('absens.tanggal', $tahun)
            ->groupBy('users.nama')
            ->get();

        // Format total jam kerja
        foreach ($rekapitulasiPerUser as $item) {
            $totalJam = floor($item->total_detik_kerja / 3600);
            $sisaDetik = $item->total_detik_kerja % 3600;
            $totalMenit = floor($sisaDetik / 60);
            $item->total_jam_kerja = "{$totalJam}j {$totalMenit}m";
        }


        // Attendance records for the current month (untuk tab "Bulan Ini" di dashboard mobile)
        // Ini tetap difilter per user yang login karena ini untuk tampilan dashboard user.
        $absenBulan = Absen::where('user_id', $userId)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal', 'desc')
            ->get();

        // Today's attendance list for all relevant users (for 'Daftar Hadir' tab di dashboard mobile)
        // Ini tetap difilter untuk hari ini.
        $daftarHadir = Absen::join('users', 'absens.user_id', '=', 'users.id')
            ->select('absens.*', 'users.nama')
            ->where('tanggal', $today)
            ->orderBy('absens.jam_masuk', 'asc')
            ->get();

        return view('dashboard', compact(
            'latestEntry',
            'selisihWaktu',
            'selisihWaktuOut',
            'bulan',
            'tahun',
            'namaBulan',
            'rekapAbsen',
            'rekapIzin',
            'alphaCount',
            'absenBulan',
            'daftarHadir',
            'cek',
            'user',
            'rekapitulasiPerUser'
        ));
    }


    /**
     * Show the form for editing the specified attendance record.
     *
     * @param Absen $absen
     * @return \Illuminate\View\View
     */
    public function edit(Absen $absen)
    {
        return view('absensi.edit', ['absen' => $absen]);
    }

    /**
     * Update the specified attendance record in storage.
     *
     * @param Absen $absen
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Absen $absen, Request $request)
    {
        // Validasi input dari form edit
        $data = $request->validate([
            'email' => 'required|email',
            'nama' => 'required|string',
            // Update status mapping: 'HADIR' (H), 'TIDAK HADIR' (A), 'IZIN' (I), 'SAKIT' (S)
            'status' => 'required|in:H,A,I,S', // Adjusted to single character statuses used in Absen model
            'keterangan' => 'nullable|string',
            'lokasi_masuk' => 'nullable|string', // This maps to original 'posisi_absen'
            'jam_masuk' => 'nullable|date_format:H:i:s', // Use date_format for time
            'jam_keluar' => 'nullable|date_format:H:i:s', // Use date_format for time
            'status_validasi' => 'required|in:0,1,2', // 0:Pending, 1:Approved, 2:Rejected
        ]);

        // Mapping request data to Absen model fields
        $updateData = [
            'email' => $data['email'],
            'nama' => $data['nama'],
            'status' => $data['status'],
            'keterangan' => $data['keterangan'] ?? '', // Ensure it's never null for DB
            'lokasi_masuk' => $data['lokasi_masuk'] ?? '', // Ensure it's never null for DB
            'jam_masuk' => $data['jam_masuk'],
            'jam_keluar' => $data['jam_keluar'],
            'status_validasi' => $data['status_validasi'],
        ];

        try {
            $absen->update($updateData);
            return Redirect::back()->with('success', 'Data absensi berhasil diperbarui!');
        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Gagal memperbarui data absensi: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified attendance record from storage.
     *
     * @param Absen $absen
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(Absen $absen)
    {
        $absen->delete();

        return redirect(route('absen.index'))->with('success', 'Absen Deleted Successfully');
    }

    /**
     * Show the user's profile for editing.
     *
     * @return \Illuminate\View\View
     */
    public function editProfile()
    {
        $email = Auth::user()->email;
        $karyawan = User::where('email', $email)->first();

        // These variables are mostly for the bottom menu logic, keeping consistency
        $hariini = date("Y-m-d");
        $currentDateTime = now();
        $latestEntry = Absen::select('*', DB::raw('CONCAT(tanggal, " ", jam_masuk) as datetime'))
            ->where('email', $email)
            ->orderBy('id', 'desc')
            ->first();

        $latestEntryOut = Absen::select('*', DB::raw('CONCAT(tanggal, " ", jam_keluar) as datetime'))
            ->where('email', $email)
            ->orderBy('id', 'desc')
            ->first();

        $selisihWaktuOut = null;
        if ($latestEntryOut && $latestEntryOut->jam_keluar) {
            $lastEntryDateTimeOut = Carbon::parse($latestEntryOut->datetime);
            $selisihWaktuOut = $currentDateTime->diffInHours($lastEntryDateTimeOut);
        }

        $cek = Absen::where('email', $email)->where('status', 'H')->whereNull('jam_keluar')->orderBy('id', 'desc')->first();


        return view('absensi.editprofile', compact('karyawan', 'selisihWaktuOut', 'cek')); // Pass $cek for bottom menu
    }

    /**
     * Update the user's profile in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateprofile(Request $request)
    {
        $nama = $request->nama_lengkap;
        $email = Auth::user()->email;
        $karyawan = User::where('email', $email)->first();

        $data = [
            'nama' => $nama,
            'email' => $email,
            'id_telegram' => $request->id_telegram,
        ];

        // Handle password update if provided
        if (!empty($request->password)) {
            $data['password'] = Hash::make($request->password);
        }

        // Handle photo upload
        if ($request->hasFile('foto')) {
            // Delete old photo if it exists and is not the default
            if ($karyawan->foto && $karyawan->foto !== 'blm.jpg') { // Assuming 'blm.jpg' is your default
                Storage::delete('public/uploads/karyawan/' . $karyawan->foto);
            }
            $foto = $email . "." . $request->file('foto')->getClientOriginalExtension();
            $data['foto'] = $foto;
            $folderPath = "public/uploads/karyawan/";
            $request->file('foto')->storeAs($folderPath, $foto);
        } else {
            $data['foto'] = $karyawan->foto; // Keep existing photo if no new one is uploaded
        }

        // Send Telegram message (ensure TelegramController and config are set up)
        // Note: This part assumes Telegram integration is fully functional.
        // $chatId = '649920017'; // Consider making this dynamic or configurable
        // $message = 'testing lol';
        // $telegramController = app(TelegramController::class);
        // $telegramController->sendMessage($chatId, $message);


        $update = User::where('email', $email)->update($data);
        if ($update) {
            return Redirect::back()->with(['success' => 'Data berhasil di update!']);
        } else {
            return Redirect::back()->with(['error' => 'Data gagal di update!']);
        }
    }

    /**
     * Display the attendance history.
     *
     * @return \Illuminate\View\View
     */
    public function histori()
    {
        $email = Auth::user()->email;
        $hariini = date("Y-m-d"); // Not directly used in view, but for consistency
        $currentDateTime = now();

        // Logic for bottom menu consistency
        $latestEntry = Absen::select('*', DB::raw('CONCAT(tanggal, " ", jam_masuk) as datetime'))
            ->where('email', $email)
            ->orderBy('id', 'desc')
            ->first();

        $latestEntryOut = Absen::select('*', DB::raw('CONCAT(tanggal, " ", jam_keluar) as datetime'))
            ->where('email', $email)
            ->orderBy('id', 'desc')
            ->first();

        $selisihWaktuOut = null;
        if ($latestEntryOut && $latestEntryOut->jam_keluar) {
            $lastEntryDateTimeOut = Carbon::parse($latestEntryOut->datetime);
            $selisihWaktuOut = $currentDateTime->diffInHours($lastEntryDateTimeOut);
        }

        $cek = Absen::where('email', $email)->where('status', 'H')->whereNull('jam_keluar')->orderBy('id', 'desc')->first();

        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        return view('absensi.histori', compact('namabulan', 'selisihWaktuOut', 'cek')); // Pass $cek
    }

    /**
     * Get attendance history for a specific month and year.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function gethistori(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $email = Auth::user()->email;

        $histori = Absen::whereRaw('MONTH(tanggal) = ?', [$bulan])
            ->whereRaw('YEAR(tanggal) = ?', [$tahun])
            ->where('email', $email)
            ->orderBy('tanggal')
            ->get();

        return view('absensi.gethistori', compact('histori'));
    }

    /**
     * Display the leave/permission requests.
     *
     * @return \Illuminate\View\View
     */
    public function izin()
    {
        $email = Auth::user()->email;
        $currentDateTime = now();

        // Logic for bottom menu consistency
        $latestEntry = Absen::select('*', DB::raw('CONCAT(tanggal, " ", jam_masuk) as datetime'))
            ->where('email', $email)
            ->orderBy('id', 'desc')
            ->first();

        $latestEntryOut = Absen::select('*', DB::raw('CONCAT(tanggal, " ", jam_keluar) as datetime'))
            ->where('email', $email)
            ->orderBy('id', 'desc')
            ->first();

        $selisihWaktuOut = null;
        if ($latestEntryOut && $latestEntryOut->jam_keluar) {
            $lastEntryDateTimeOut = Carbon::parse($latestEntryOut->datetime);
            $selisihWaktuOut = $currentDateTime->diffInHours($lastEntryDateTimeOut);
        }

        $cek = Absen::where('email', $email)->where('status', 'H')->whereNull('jam_keluar')->orderBy('id', 'desc')->first();

        // Mengambil semua data izin tanpa filter jabatan
        $dataizin = Pengajuan_Izin::get();

        return view('absensi.izin.izin', compact('dataizin', 'selisihWaktuOut', 'cek')); // Pass $cek
    }

    /**
     * Show the form for creating a new leave/permission request.
     *
     * @return \Illuminate\View\View
     */
    public function buatizin()
    {
        $email = Auth::user()->email;
        $currentDateTime = now();

        // Logic for bottom menu consistency
        $latestEntry = Absen::select('*', DB::raw('CONCAT(tanggal, " ", jam_masuk) as datetime'))
            ->where('email', $email)
            ->orderBy('id', 'desc')
            ->first();

        $latestEntryOut = Absen::select('*', DB::raw('CONCAT(tanggal, " ", jam_keluar) as datetime'))
            ->where('email', $email)
            ->orderBy('id', 'desc')
            ->first();

        $selisihWaktuOut = null;
        if ($latestEntryOut && $latestEntryOut->jam_keluar) {
            $lastEntryDateTimeOut = Carbon::parse($latestEntryOut->datetime);
            $selisihWaktuOut = $currentDateTime->diffInHours($lastEntryDateTimeOut);
        }

        $cek = Absen::where('email', $email)->where('status', 'H')->whereNull('jam_keluar')->orderBy('id', 'desc')->first();

        return view('absensi.izin.buatizin', compact('selisihWaktuOut', 'cek')); // Pass $cek
    }

    /**
     * Store a newly created leave/permission request in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeizin(Request $request)
    {
        $request->validate([
            'tanggal_izin' => 'required|date',
            'status' => 'required|in:IZIN,SAKIT',
            'keterangan' => 'required|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Max 2MB
        ]);

        $email = Auth::user()->email;
        $tanggal = $request->tanggal_izin;
        $status = $request->status;
        $keterangan = $request->keterangan;
        $nama = User::where('email', $email)->pluck('nama')->first();
        // Mengambil semua ID Telegram SUPERADMIN untuk notifikasi
        $idTelegram = User::where('jabatan', 'SUPERADMIN')->pluck('id_telegram')->toArray();

        $fotoName = null;
        if ($request->hasFile('foto')) {
            $fotoName = $status . "-" . $tanggal . "-" . $email . "." . $request->file('foto')->getClientOriginalExtension();
            $folderPath = "public/uploads/izin/";
            $request->file('foto')->storeAs($folderPath, $fotoName);
        }

        $data = [
            'email' => $email,
            'tanggal_izin' => $tanggal,
            'status' => $status,
            'keterangan' => $keterangan,
            'evident' => $fotoName,
            'status_approved' => 0, // Default to pending
        ];

        $message = "PENGAJUAN IZIN \n\n$nama mengajukan pengajuan $status \nuntuk tanggal $tanggal \n\nDengan keterangan: \n$keterangan";

        // Send the message using TelegramController
        // Ensure TelegramController and sendMessage method are correctly implemented
        if (class_exists(\App\Http\Controllers\TelegramController::class)) { // Fully qualify class name
            $telegramController = app(\App\Http\Controllers\TelegramController::class);
            foreach ($idTelegram as $chatId) {
                $telegramController->sendMessage($chatId, $message);
            }
        }

        try {
            Pengajuan_Izin::create($data); // Use create for new records
            return redirect(route('absen.izin'))->with(['success' => 'Form berhasil dibuat.']);
        } catch (\Exception $e) {
            return redirect(route('absen.izin'))->with(['error' => 'Form gagal dibuat. Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the monitoring page for attendance.
     *
     * @return \Illuminate\View\View
     */
    public function monitor()
    {
        // Mengambil jumlah semua pengajuan izin yang belum disetujui
        $jumlahIzin = Pengajuan_Izin::where('status_approved', 0)->count();

        return view('absensi.monitor', compact('jumlahIzin'));
    }

    /**
     * Get attendance data for a specific date for monitoring.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function getpresensi(Request $request)
    {
        $tanggal = $request->tanggal;

        // Mengambil jumlah semua pengajuan izin yang belum disetujui
        $jumlahIzin = Pengajuan_Izin::where('status_approved', 0)->count();

        // Mengambil semua data absen untuk tanggal tertentu tanpa filter jabatan
        $absen = Absen::query()->where('tanggal', $tanggal)->get();

        return view('absensi.getpresensi', compact('absen', 'jumlahIzin'));
    }

    /**
     * Get attendance records via AJAX.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function records(Request $request)
    {
        $tanggal = $request->tanggal;

        if ($request->ajax()) {
            $absens = Absen::when($tanggal, function ($query, $tanggal) {
                return $query->where('tanggal', '=', $tanggal);
            })->get();

            return response()->json([
                'absens' => $absens
            ]);
        } else {
            abort(403);
        }
    }

    /**
     * Get rekap presensi data for monitoring.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function getRekapPresensi(Request $request)
    {
        $tanggal = $request->tanggal; // This should be 'YYYY-MM' for month-year filter

        // Mengambil jumlah semua pengajuan izin yang belum disetujui
        $jumlahIzin = Pengajuan_Izin::where('status_approved', 0)->count();

        // Mengambil semua data absen untuk bulan-tahun tertentu tanpa filter jabatan
        $absen = Absen::query()->whereRaw('DATE_FORMAT(tanggal, "%Y-%m") = ?', [$tanggal])->get();

        return view('absensi.getrekappresensi', compact('absen', 'jumlahIzin'));
    }

    /**
     * Display map for a specific attendance record.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function showmap(Request $request)
    {
        $id = $request->id;
        $absen = Absen::where('id', $id)->first();

        return view('absensi.showmap', compact('absen'));
    }

    /**
     * Preview data for individual attendance reports.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function previewDataLaporan(Request $request)
    {
        $email = $request->email;
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        $query = Absen::select(
            'absens.*',
            DB::raw('FLOOR(TIMESTAMPDIFF(SECOND, jam_masuk, jam_keluar) / 3600) as total_hours'),
            DB::raw('FLOOR((TIMESTAMPDIFF(SECOND, jam_masuk, jam_keluar) % 3600) / 60) as total_minutes')
        )
            ->whereRaw('MONTH(tanggal) = ?', [$bulan])
            ->whereRaw('YEAR(tanggal) = ?', [$tahun]);

        // Mengambil semua data laporan tanpa filter jabatan spesifik
        // Jika $email tidak kosong, filter berdasarkan email tersebut.
        // Jika $email kosong, ambil semua.
        if (!empty($email)) {
            $previewData = $query->where('email', $email)->get();
        } else {
            $previewData = $query->get();
        }


        foreach ($previewData as $data) {
            if ($data->total_hours !== null && $data->total_hours >= 0 && $data->jam_keluar !== null) { // Check if clocked out
                $data->total_time = $data->total_hours . ' jam ' . $data->total_minutes . ' menit';
            } else {
                $data->total_time = '-';
            }
        }

        return response()->json($previewData);
    }

    /**
     * Preview data for aggregated attendance reports (rekap).
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function previewDataRekap(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $totalDays = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);

        $selectClause = 'email, nama';
        $totalHoursClause = "0";
        $totalMinutesClause = "0";

        for ($day = 1; $day <= $totalDays; $day++) {
            $dayClause = "
                MAX(CASE
                    WHEN DAY(tanggal) = $day THEN
                        CASE
                            WHEN status = 'H' THEN CONCAT_WS('-', COALESCE(jam_masuk, ''), COALESCE(jam_keluar, ''))
                            WHEN status = 'I' THEN 'I'
                            WHEN status = 'S' THEN 'S'
                            ELSE ''
                        END
                    ELSE
                        CASE
                            WHEN DAYNAME(CONCAT(?, '-', ?, '-', $day)) = 'Sunday' THEN 'LIBUR'
                            ELSE ''
                        END
                END)
            ";
            // Pass year and month as parameters to prevent SQL injection in DAYNAME
            $selectClause .= ", " . str_replace(['?', '?'], [$tahun, $bulan], $dayClause) . " as tgl_$day";

            $hoursClause = "
                MAX(CASE
                    WHEN DAY(tanggal) = $day AND status = 'H' THEN FLOOR(TIMESTAMPDIFF(SECOND, jam_masuk, jam_keluar) / 3600)
                    ELSE 0
                END)
            ";
            $minutesClause = "
                MAX(CASE
                    WHEN DAY(tanggal) = $day AND status = 'H' THEN FLOOR((TIMESTAMPDIFF(SECOND, jam_masuk, jam_keluar) % 3600) / 60)
                    ELSE 0
                END)
            ";

            $selectClause .= ", " . $hoursClause . " as total_hours_$day";
            $selectClause .= ", " . $minutesClause . " as total_minutes_$day";

            $totalHoursClause .= " + COALESCE(" . $hoursClause . ", 0)";
            $totalMinutesClause .= " + COALESCE(" . $minutesClause . ", 0)";
        }

        $selectClause .= ", (" . $totalHoursClause . ") as total_hours_month_raw";
        $selectClause .= ", (" . $totalMinutesClause . ") as total_minutes_month_raw";

        $query = Absen::selectRaw($selectClause)
            ->whereRaw('MONTH(tanggal) = ?', [$bulan])
            ->whereRaw('YEAR(tanggal) = ?', [$tahun]);

        // Mengambil semua data rekap tanpa filter jabatan spesifik
        $previewData = $query->groupByRaw('email, nama')->get();

        // Post-process to correct the total hours and minutes for the month
        foreach ($previewData as $data) {
            $total_minutes = $data->total_minutes_month_raw;
            $additional_hours = floor($total_minutes / 60);
            $remaining_minutes = $total_minutes % 60;

            $data->total_hours_month = $data->total_hours_month_raw + $additional_hours;
            $data->total_minutes_month = $remaining_minutes;
        }

        return response()->json($previewData);
    }

    /**
     * Display the attendance report generation page.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function laporan(Request $request)
    {
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

        // Mengambil jumlah semua pengajuan izin yang belum disetujui
        $jumlahIzin = Pengajuan_Izin::where('status_approved', 0)->count();

        // Mengambil semua user tanpa filter jabatan
        $user = User::orderBy('nama')->get();

        // $absen is not directly used in the initial load of the 'laporan' view,
        // it's typically fetched via AJAX by previewDataLaporan.
        $absen = collect(); // Initialize as empty collection

        return view('absensi.laporan.laporan', compact('namabulan', 'user', 'jumlahIzin', 'absen'));
    }

    /**
     * Generate and optionally export attendance report.
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function cetaklaporan(Request $request)
    {
        $email = $request->email;
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

        // Jika email tidak disediakan, ambil semua user. Jika disediakan, ambil user spesifik.
        if (!empty($email)) {
            $user = User::where('email', $email)->first();
        } else {
            // Jika Anda mencetak laporan untuk semua, Anda mungkin tidak butuh objek $user tunggal di sini.
            // Pertimbangkan bagaimana view cetaklaporan menggunakan $user.
            $user = null; // Atau ambil semua user jika view mendukungnya
        }


        $absen = Absen::where('email', $email)
            ->select(
                'absens.*',
                DB::raw('FLOOR(TIMESTAMPDIFF(SECOND, jam_masuk, jam_keluar) / 3600) as total_hours'),
                DB::raw('FLOOR((TIMESTAMPDIFF(SECOND, jam_masuk, jam_keluar) % 3600) / 60) as total_minutes')
            )
            ->whereRaw('MONTH(tanggal) = ?', [$bulan])
            ->whereRaw('YEAR(tanggal) = ?', [$tahun])
            ->orderBy('tanggal')
            ->get();

        foreach ($absen as $data) {
            if ($data->total_hours !== null && $data->total_hours >= 0 && $data->jam_keluar !== null) {
                $data->total_time = $data->total_hours . ' jam ' . $data->total_minutes . ' menit';
            } else {
                $data->total_time = '-';
            }
        }

        if ($request->has('exportExcel')) {
            $time = date("d-m-Y H:i:s");
            // Laravel way to handle file downloads for Excel
            return response()->streamDownload(function () use ($bulan, $tahun, $namabulan, $user, $absen) {
                echo view('absensi.laporan.cetaklaporan', compact('bulan', 'tahun', 'namabulan', 'user', 'absen'))->render();
            }, "Laporan Absensi $time.xls", [
                'Content-Type' => 'application/vnd.ms-excel',
                'Content-Disposition' => 'attachment; filename="Laporan Absensi ' . $time . '.xls"',
            ]);
        }

        return view('absensi.laporan.cetaklaporan', compact('bulan', 'tahun', 'namabulan', 'user', 'absen'));
    }

    /**
     * Display the rekap attendance report generation page.
     *
     * @return \Illuminate\View\View
     */
    public function rekap()
    {
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        // Mengambil jumlah semua pengajuan izin yang belum disetujui
        $jumlahIzin = Pengajuan_Izin::where('status_approved', 0)->count();

        return view('absensi.laporan.rekap', compact('namabulan', 'jumlahIzin'));
    }

    /**
     * Generate and optionally export rekap attendance report.
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function cetakrekap(Request $request)
    {
        $bulan = str_pad($request->bulan, 2, "0", STR_PAD_LEFT);
        $bulans = $request->bulan; // Original month number
        $tahun = $request->tahun;
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        $totalDays = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);

        $rekapQuery = Absen::select([
            "users.perner",
            "users.jabatan",
            "absens.email",
            "absens.nama",
            "absens.status",
            "absens.tanggal",
            "absens.jam_masuk",
            "absens.jam_keluar",
            DB::raw("DAYNAME(absens.tanggal) AS hari"),
            DB::raw("DAY(absens.tanggal) AS date"),
            DB::raw('
                FLOOR(TIMESTAMPDIFF(SECOND, absens.jam_masuk, absens.jam_keluar) / 3600) as total_hours,
                FLOOR((TIMESTAMPDIFF(SECOND, absens.jam_masuk, absens.jam_keluar) % 3600) / 60) as total_minutes
            ')
        ])
            ->leftJoin('users', 'absens.user_id', '=', 'users.id') // Join on user_id
            ->whereRaw('MONTH(absens.tanggal) = ?', [$bulan])
            ->whereRaw('YEAR(absens.tanggal) = ?', [$tahun]);

        // Mengambil semua data rekap tanpa filter jabatan spesifik
        $rekap = $rekapQuery->groupByRaw('absens.email, absens.nama')->get();

        $result = [];
        foreach ($rekap as $item) {
            if (!array_key_exists($item->email, $result)) {
                $result[$item->email] = [
                    "perner" => $item->perner,
                    "jabatan" => $item->jabatan,
                    "nama" => $item->nama,
                    "email" => $item->email,
                    "total_hours_month" => 0,
                    "total_minutes_month" => 0,
                ];

                for ($day = 1; $day <= $totalDays; $day++) {
                    $dateForDay = Carbon::createFromFormat("Y-m-d", "{$tahun}-{$bulan}-{$day}");
                    if ($dateForDay->dayOfWeek === Carbon::SUNDAY) { // Use Carbon constant for Sunday
                        $result[$item->email]['tgl_' . $day] = "LIBUR";
                    } else {
                        $result[$item->email]['tgl_' . $day] = ""; // Default to empty if no attendance
                    }
                    $result[$item->email]['total_hours_' . $day] = 0;
                    $result[$item->email]['total_minutes_' . $day] = 0;
                }
            }

            // Update attendance for the specific day
            $result[$item->email]['tgl_' . $item->date] = match ($item->status) {
                "H" => "{$item->jam_masuk}-{$item->jam_keluar}",
                'I' => 'I',
                'S' => 'S',
                default => "A" // Assume 'A' (Alpha) for other statuses or null
            };

            // Aggregate total hours and minutes for the month
            if ($item->status == 'H' && $item->total_hours !== null && $item->total_minutes !== null) {
                $result[$item->email]['total_hours_' . $item->date] = $item->total_hours;
                $result[$item->email]['total_minutes_' . $item->date] = $item->total_minutes;

                $result[$item->email]['total_hours_month'] += $item->total_hours;
                $result[$item->email]['total_minutes_month'] += $item->total_minutes;
            }
        }

        // Final adjustment for total minutes to hours conversion
        foreach ($result as $email => $data) {
            $total_minutes = $data['total_minutes_month'];
            $additional_hours = floor($total_minutes / 60);
            $remaining_minutes = $total_minutes % 60;

            $result[$email]['total_hours_month'] += $additional_hours;
            $result[$email]['total_minutes_month'] = $remaining_minutes;
        }

        if ($request->has('exportExcel')) {
            $time = date("d-m-Y H:i:s");
            return response()->streamDownload(function () use ($bulan, $tahun, $rekap, $namabulan, $bulans, $result, $totalDays) {
                echo view('absensi.laporan.cetakrekap', compact('bulan', 'tahun', 'rekap', 'namabulan', 'bulans', 'result', 'totalDays'))->render();
            }, "Rekap Absensi $time.xls", [
                'Content-Type' => 'application/vnd.ms-excel',
                'Content-Disposition' => 'attachment; filename="Rekap Absensi ' . $time . '.xls"',
            ]);
        }

        return view('absensi.laporan.cetakrekap', compact('bulan', 'tahun', 'rekap', 'namabulan', 'bulans', 'result', 'totalDays'));
    }

    /**
     * Display leave/sick requests for approval.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function izinsakit(Request $request)
    {
        $query = Pengajuan_Izin::select('pengajuan_izin.id', 'tanggal_izin', 'pengajuan_izin.email', 'users.nama', 'users.jabatan', 'status', 'status_approved', 'keterangan', 'evident')
            ->join('users', 'pengajuan_izin.email', '=', 'users.email');

        // Mengambil semua pengajuan izin tanpa filter jabatan
        $izinsakit = $query->orderBy('tanggal_izin', 'desc')->get();

        // Mengambil jumlah semua pengajuan izin yang belum disetujui
        $jumlahIzin = Pengajuan_Izin::where('status_approved', 0)->count();

        return view('absensi.izin.izinsakit', compact('izinsakit', 'jumlahIzin'));
    }

    /**
     * Approve or reject leave/sick requests.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function action(Request $request)
    {
        $request->validate([
            'status_approved' => 'required|in:0,1,2', // 0: Pending, 1: Approved, 2: Rejected
            'id_izin_form' => 'required|exists:pengajuan_izin,id',
            'status_izin_form' => 'required|in:IZIN,SAKIT',
            'tanggal_izin_form' => 'required|date',
            'evident_izin_form' => 'nullable|string',
            'nama_izin_form' => 'required|string',
            'email_izin_form' => 'required|email|exists:users,email',
            'keterangan_izin_form' => 'nullable|string', // DITAMBAHKAN: untuk mengambil keterangan dari form
        ]);

        $status_approved = $request->status_approved;
        $id_izin_form = $request->id_izin_form;
        $status_izin_form = $request->status_izin_form;
        $tanggal = $request->tanggal_izin_form;
        $evident = $request->evident_izin_form; // This is the filename string
        $nama = $request->nama_izin_form;
        $email = $request->email_izin_form;
        $keterangan_izin = $request->keterangan_izin_form;
        $user = User::where('email', $email)->first(); // Use $user directly
        $user_id = $user->id;

        if ($status_approved == 1) { // If approving the request
            $status = ($status_izin_form == "SAKIT") ? "S" : "I";

            // Check if an attendance record already exists for this user and date
            $existingAbsen = Absen::where('user_id', $user_id)
                ->where('tanggal', $tanggal)
                ->first();

            $dataAbsen = [
                'user_id' => $user_id,
                'email' => $email,
                'nama' => $nama,
                'status' => $status,
                'keterangan' => $keterangan_izin, // Mengambil keterangan dari form
                'tanggal' => $tanggal,
                'tanggal_keluar' => $tanggal, // Biasanya sama dengan tanggal masuk untuk izin/sakit
                'jam_masuk' => "00:00:00",
                'jam_keluar' => "00:00:00",
                'foto_masuk' => $evident ?? '',
                'foto_keluar' => $evident ?? '',
                'lokasi_masuk' => "Izin/Sakit", // Placeholder for location
                'lokasi_keluar' => "Izin/Sakit", // Placeholder for location
                'laporan_masuk' => $status_izin_form, // Keep for historical context of the izin/sakit
                'laporan_keluar' => $status_izin_form,
                'status_validasi' => 1, // Indicate it's from an approved izin/sakit
            ];

            if ($existingAbsen) {
                // Update existing record (e.g., if it was an 'A' for Alpha)
                $existingAbsen->update($dataAbsen);
            } else {
                // Create a new record
                Absen::create($dataAbsen);
            }
        } else if ($status_approved == 2) { // If rejecting, remove any associated attendance record if it was an I/S
            Absen::where('user_id', $user_id)
                ->where('tanggal', $tanggal)
                ->whereIn('status', ['I', 'S'])
                ->delete();
        }

        $update = Pengajuan_Izin::where('id', $id_izin_form)->update([
            'status_approved' => $status_approved,
        ]);

        if ($update) {
            return Redirect::back()->with(['success' => 'Status pengajuan berhasil di Update']);
        } else {
            return Redirect::back()->with(['warning' => 'Status pengajuan gagal di Update']);
        }
    }

    /**
     * Revert approval status of a leave/sick request.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function batalapprove($id)
    {
        $izin = Pengajuan_Izin::find($id);
        if (!$izin) {
            return Redirect::back()->with(['warning' => 'Pengajuan izin tidak ditemukan.']);
        }

        // Remove the associated attendance record if it was an I/S entry
        // Ensure user exists before trying to get ID
        $user = User::where('email', $izin->email)->first();
        if ($user) {
            Absen::where('user_id', $user->id)
                ->where('tanggal', $izin->tanggal_izin)
                ->whereIn('status', ['I', 'S'])
                ->delete();
        }

        $update = $izin->update([
            'status_approved' => 0, // Revert to pending
        ]);

        if ($update) {
            return Redirect::back()->with(['success' => 'Status pengajuan berhasil dibatalkan']);
        } else {
            return Redirect::back()->with(['warning' => 'Status pengajuan gagal dibatalkan']);
        }
    }

    /**
     * Check if a leave/permission request already exists for a given date.
     *
     * @param Request $request
     * @return int
     */
    public function cekizin(Request $request)
    {
        $tanggal = $request->tanggal_izin;
        $email = Auth::user()->email;

        $cek = Pengajuan_Izin::where('email', $email)->where('tanggal_izin', $tanggal)->count();

        return $cek;
    }

    public function updateValidationStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:absens,id',
            'status_validasi' => 'required|in:0,1,2', // 0:Pending, 1:Approved, 2:Rejected
        ]);

        try {
            $absen = Absen::find($request->id);
            if (!$absen) {
                return response()->json(['success' => false, 'message' => 'Record absensi tidak ditemukan.'], 404);
            }

            $absen->status_validasi = $request->status_validasi;
            $absen->save();

            return response()->json(['success' => true, 'message' => 'Status validasi berhasil diperbarui.'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui status validasi. Error: ' . $e->getMessage()], 500);
        }
    }
}
