<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalSkripsi extends Model
{
    protected $table = 'jadwal_skripsi';

    protected $fillable = [
        'pendaftaran_id',
        'tanggal',
        'waktu_mulai',
        'waktu_selesai',
        'ruang',
        'dosen_penguji_1',
        'dosen_penguji_2',
        'dosen_penguji_3',
        'keterangan',
        'status'
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

    public function penilaian()
    {
        return $this->hasMany(PenilaianSkripsi::class, 'jadwal_skripsi_id');
    }

    public function getNilaiKumulatif()
    {
        $penelianList = $this->penilaian()->get();
        $result = [];

        $pembimbing = $this->pendaftaran->dosen_pembimbing;

        $sorted = [];
        $pengujiLain = [];

        foreach ($penelianList as $p) {
            if ($p->nama_dosen_penguji == $pembimbing) {
                $sorted[] = $p;
            } else {
                $pengujiLain[] = $p;
            }
        }

        $sorted = array_merge($sorted, $pengujiLain);

        $totalNilai = 0;
        foreach ($sorted as $index => $p) {
            $bobot = ($index == 0) ? 40 : 30;
            $jumlah = ($p->nilai_akhir * $bobot / 100);
            $totalNilai += $jumlah;

            $result[] = [
                'nama' => $p->nama_dosen_penguji,
                'bobot' => $bobot,
                'nilai' => $p->nilai_akhir,
                'jumlah' => $jumlah
            ];
        }

        return [
            'details' => $result,
            'total' => $totalNilai,
            'huruf_mutu' => $this->getHurufMutu($totalNilai)
        ];
    }

    private function getHurufMutu($nilai)
    {
        if ($nilai <= 44.00) return 'E';
        if ($nilai <= 55.49) return 'D';
        if ($nilai <= 59.49) return 'C';
        if ($nilai <= 63.49) return 'C+';
        if ($nilai <= 67.49) return 'B-';
        if ($nilai <= 71.49) return 'B';
        if ($nilai <= 75.49) return 'B+';
        if ($nilai <= 79.50) return 'A-';
        return 'A';
    }
}
