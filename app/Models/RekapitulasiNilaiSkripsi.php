<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekapitulasiNilaiSkripsi extends Model
{
    use HasFactory;

    protected $table = 'rekapitulasi_nilai_skripsi';

    protected $fillable = [
        'jadwal_skripsi_id',
        'nilai_akhir',
        'huruf_mutu',
        'catatan_kumulatif',
        'is_published'
    ];

    public function jadwal()
    {
        return $this->belongsTo(JadwalSkripsi::class, 'jadwal_skripsi_id');
    }

    public function hitungHurufMutu()
    {
        $nilai = $this->nilai_akhir;

        if ($nilai <= 44.00) {
            return 'E';
        } elseif ($nilai <= 55.49) {
            return 'D';
        } elseif ($nilai <= 59.49) {
            return 'C';
        } elseif ($nilai <= 63.49) {
            return 'C+';
        } elseif ($nilai <= 67.49) {
            return 'B-';
        } elseif ($nilai <= 71.49) {
            return 'B';
        } elseif ($nilai <= 75.49) {
            return 'B+';
        } elseif ($nilai <= 79.50) {
            return 'A-';
        } else {
            return 'A';
        }
    }
}
