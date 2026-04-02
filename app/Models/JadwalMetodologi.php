<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalMetodologi extends Model
{
    protected $table = 'jadwal_metodologi';

    protected $fillable = [
        'pendaftaran_id', 'tanggal', 'waktu_mulai', 'waktu_selesai',
        'ruang', 'dosen_penguji_1', 'dosen_penguji_2',
        'keterangan', 'status'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
    ];

    public function pendaftaran()
    {
        return $this->belongsTo(PendaftaranMetodologi::class, 'pendaftaran_id');
    }
}