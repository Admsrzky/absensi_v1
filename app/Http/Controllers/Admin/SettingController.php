<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting; // Import model Setting
use Illuminate\Support\Facades\Redirect;

class SettingController extends Controller
{
    public function index()
    {
        // Ambil semua pengaturan jam absen
        $settings = Setting::whereIn('key', ['jam_masuk_start', 'jam_masuk_end', 'jam_keluar_min'])
            ->pluck('value', 'key')
            ->toArray();

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'jam_masuk_start' => 'required|date_format:H:i:s',
            'jam_masuk_end' => 'required|date_format:H:i:s|after:jam_masuk_start',
            'jam_keluar_min' => 'required|date_format:H:i:s|after:jam_masuk_end', // Optional: Ensure clock-out is after clock-in end
        ]);

        try {
            Setting::updateOrCreate(['key' => 'jam_masuk_start'], ['value' => $request->jam_masuk_start]);
            Setting::updateOrCreate(['key' => 'jam_masuk_end'], ['value' => $request->jam_masuk_end]);
            Setting::updateOrCreate(['key' => 'jam_keluar_min'], ['value' => $request->jam_keluar_min]);

            return Redirect::back()->with('success', 'Pengaturan jam absensi berhasil diperbarui!');
        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Gagal memperbarui pengaturan jam absensi: ' . $e->getMessage());
        }
    }
}
