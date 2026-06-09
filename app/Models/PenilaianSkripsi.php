<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenilaianSkripsi extends Model
{
    use HasFactory;

    protected $table = 'penilaian_skripsi';

    protected $fillable = [
        'jadwal_skripsi_id',
        'dosen_penguji_id',
        'nama_dosen_penguji',
        'bobot',
        'nilai',
        'jumlah',
        'nilai_fenomena',
        'nilai_variabel',
        'nilai_teori_metode',
        'nilai_alat_ukur',
        'nilai_analisis',
        'nilai_simpulan_saran',
        'nilai_presentasi',
        'nilai_akhir',
        'catatan',
        'is_completed'
    ];

    protected $casts = [
        'is_completed' => 'boolean'
    ];

    public function jadwal()
    {
        return $this->belongsTo(JadwalSkripsi::class, 'jadwal_skripsi_id');
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class, 'dosen_penguji_id');
    }

    // Hitung jumlah dari aspek-aspek
    public function hitungJumlah()
    {
        $total = 0;
        $total += ($this->nilai_fenomena ?? 0) * 2;   // bobot 2
        $total += ($this->nilai_variabel ?? 0) * 2;   // bobot 2
        $total += ($this->nilai_teori_metode ?? 0) * 2; // bobot 2
        $total += ($this->nilai_alat_ukur ?? 0) * 1;   // bobot 1
        $total += ($this->nilai_analisis ?? 0) * 1;    // bobot 1
        $total += ($this->nilai_simpulan_saran ?? 0) * 1; // bobot 1
        $total += ($this->nilai_presentasi ?? 0) * 1;    // bobot 1

        $this->jumlah = $total;

        // Hitung nilai rata-rata (total / 10)
        $this->nilai_akhir = $total / 10;

        return $this;
    }
}
