<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalSkripsi extends Model
{
    protected $table = 'jadwal_skripsi';

    protected $fillable = [
        'pendaftaran_id', 'tanggal', 'waktu_mulai', 'waktu_selesai',
        'ruang', 'dosen_penguji_1', 'dosen_penguji_2', 'dosen_penguji_3',
        'keterangan', 'status'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
    ];

    public function pendaftaran()
    {
        return $this->belongsTo(PendaftaranSkripsi::class, 'pendaftaran_id');
    }
}