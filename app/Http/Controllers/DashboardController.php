<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Absen;
use App\Models\User;
use App\Models\Pengajuan_Izin;
use Illuminate\Support\Facades\Storage; // Not directly used here, but often present
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Hash; // Not directly used here
// use Illuminate\Support\Facades\Redirect; // Not directly used here

class DashboardController extends Controller
{
    /**
     * Display the user dashboard (mobile view).
     * This method is called by routes like /dashboard and /.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        $email = auth()->user()->email;
        $hariini = date("Y-m-d");
        $tahun = date('Y');
        $bulan = date('m') * 1;

        // Get the latest attendance entry for the current user and today
        $latestEntry = Absen::where('user_id', Auth::id()) // Using Auth::id() for consistency
            ->where('tanggal', $hariini)
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
        $cek = Absen::where('user_id', Auth::id()) // Using Auth::id() for consistency
            ->where('tanggal', $hariini)
            ->whereNotNull('jam_masuk')
            ->whereNull('jam_keluar')
            ->first();

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

        // Rekap Absen Bulan Ini (Hadir) for the logged-in user
        $rekapAbsen = Absen::selectRaw('COUNT(id) as jumlah_hadir')
            ->where('user_id', Auth::id())
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->where('status', 'H')
            ->first();

        // Rekap Izin/Sakit Bulan Ini for the logged-in user
        $rekapIzin = Pengajuan_Izin::selectRaw('SUM(IF(status="SAKIT",1,0)) AS jumlah_sakit, SUM(IF(status="IZIN",1,0)) AS jumlah_izin')
            ->where('email', $email)
            ->where('status_approved', 1)
            ->whereRaw('MONTH(tanggal_izin) = ?', [$bulan])
            ->whereRaw('YEAR(tanggal_izin) = ?', [$tahun])
            ->first();

        // Monthly attendance data for the logged-in user
        $absenBulan = Absen::where('user_id', Auth::id())
            ->whereRaw('MONTH(tanggal) = ?', [$bulan])
            ->whereRaw('YEAR(tanggal) = ?', [$tahun])
            ->orderBy('tanggal')->get();

        // Today's attendance list (for all users, if reused in admin dashboard)
        // For consistency with the mobile dashboard, we'll fetch all users' attendance today.
        $daftarHadir = Absen::join('users', 'absens.user_id', '=', 'users.id')
            ->select('absens.*', 'users.nama')
            ->where('tanggal', $hariini)
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
            'absenBulan',
            'daftarHadir',
            'cek'
        ));
    }

    /**
     * Display the admin dashboard.
     * This method is called by the /dashboardadmin route.
     *
     * @return \Illuminate\View\View
     */
    public function dashboardadmin()
    {
        $tahun = date('Y');
        $bulan = date('m') * 1;
        $hariini = date("Y-m-d");
        $userId = Auth::id(); // Get the logged-in admin's user ID

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

        // --- Variables for the admin's personal attendance cards (Masuk/Keluar) ---
        // These are needed because dashboard.blade.php still has the HTML for them.
        // We'll populate them with the admin's own data.
        $latestEntry = Absen::where('user_id', $userId)
            ->where('tanggal', $hariini)
            ->orderBy('id', 'desc')
            ->first();

        $selisihWaktu = null;
        if ($latestEntry && $latestEntry->jam_masuk) {
            $jamMasuk = Carbon::parse($latestEntry->jam_masuk);
            $selisihWaktu = Carbon::now()->diffInMinutes($jamMasuk);
        }

        $selisihWaktuOut = null;
        if ($latestEntry && $latestEntry->jam_keluar) {
            $jamKeluar = Carbon::parse($latestEntry->jam_keluar);
            $selisihWaktuOut = Carbon::now()->diffInMinutes($jamKeluar);
        }

        $cek = Absen::where('user_id', $userId)
            ->where('tanggal', $hariini)
            ->whereNotNull('jam_masuk')
            ->whereNull('jam_keluar')
            ->first();

        // These are typically for the mobile dashboard's tabs, but `dashboard.blade.php` expects them.
        // We'll populate them with the admin's own data for now, though they are commented out in the admin.dashboardadmin.blade.php
        $absenBulan = Absen::where('user_id', $userId)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal', 'desc')
            ->get();

        $daftarHadir = Absen::join('users', 'absens.user_id', '=', 'users.id')
            ->select('absens.*', 'users.nama')
            ->where('tanggal', $hariini)
            ->orderBy('absens.jam_masuk', 'asc')
            ->get();
        // --- End of personal attendance variables ---


        // Total Karyawan (untuk card pertama - semua user)
        $user = User::count();

        // Jumlah pengajuan izin yang belum disetujui (untuk badge notifikasi di sidebar)
        $jumlahIzin = Pengajuan_Izin::where('status_approved', 0)->count();

        // Rekap Absen Bulan Ini (Hadir) - untuk semua karyawan
        $rekapAbsen = Absen::selectRaw('COUNT(id) as jumlah_hadir')
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->where('status', 'H')
            ->first();

        // Rekap Izin/Sakit Bulan Ini - untuk semua karyawan
        $rekapIzin = Absen::selectRaw('SUM(CASE WHEN status = "I" THEN 1 ELSE 0 END) as jumlah_izin,
                                            SUM(CASE WHEN status = "S" THEN 1 ELSE 0 END) as jumlah_sakit')
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->first();

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

        // Rekapitulasi Per User untuk Tabel (semua karyawan)
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

        return view('dashboardadmin', compact( // Render view admin.dashboardadmin
            'bulan',
            'tahun',
            'namaBulan',
            'rekapAbsen',
            'rekapIzin',
            'alphaCount',
            'user',
            'jumlahIzin',
            'rekapitulasiPerUser',
            'latestEntry',
            'selisihWaktu',
            'selisihWaktuOut',
            'cek', // Added for admin's personal attendance cards
            'absenBulan',
            'daftarHadir' // Added for admin's personal attendance tabs (if dashboard.blade.php uses them)
        ));
    }
}
