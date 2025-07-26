<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User; // Pastikan ini diimpor jika belum

class Absen extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama',
        'email',
        'status',
        'keterangan', // Pastikan ini ada dan akan diisi jika relevan
        'tanggal',
        'tanggal_keluar', // Pastikan ini ada dan akan diisi jika relevan
        'jam_masuk',
        'jam_keluar',
        'foto_masuk',
        'foto_keluar',
        'lokasi_masuk',
        'lokasi_keluar',
        'laporan_masuk',
        'laporan_keluar',
        'status_validasi', // <-- DITAMBAHKAN: Penting untuk konsistensi dengan controller
    ];

    /**
     * Get the user that owns the attendance record.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
