<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedbackSkripsi extends Model
{
    use HasFactory;

    protected $table = 'feedback_skripsi';

    protected $fillable = [
        'jadwal_skripsi_id',
        'dosen_id',
        'nama_dosen',
        'nilai_bagian_depan',
        'nilai_bab1',
        'nilai_bab2',
        'nilai_bab3',
        'nilai_bab4',
        'nilai_bab5',
        'nilai_daftar_pustaka',
        'rekomendasi',
        'catatan_perbaikan',
        'perbaikan_mayor',
        'perbaikan_minor',
        'file_catatan',
        'is_completed',
        'catatan_bagian_depan',
        'catatan_bab1',
        'catatan_bab2',
        'catatan_bab3',
        'catatan_bab4',
        'catatan_bab5',
        'catatan_daftar_pustaka',
        'catatan_presentasi'
    ];

    protected $casts = [
        'perbaikan_mayor' => 'boolean',
        'perbaikan_minor' => 'boolean',
        'is_completed' => 'boolean'
    ];

    public function jadwal()
    {
        return $this->belongsTo(JadwalSkripsi::class, 'jadwal_skripsi_id');
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class, 'dosen_id');
    }

    // Hitung total nilai rata-rata
    public function getTotalNilaiAttribute()
    {
        $total = 0;
        $count = 0;

        if ($this->nilai_bagian_depan) {
            $total += $this->nilai_bagian_depan;
            $count++;
        }
        if ($this->nilai_bab1) {
            $total += $this->nilai_bab1;
            $count++;
        }
        if ($this->nilai_bab2) {
            $total += $this->nilai_bab2;
            $count++;
        }
        if ($this->nilai_bab3) {
            $total += $this->nilai_bab3;
            $count++;
        }
        if ($this->nilai_bab4) {
            $total += $this->nilai_bab4;
            $count++;
        }
        if ($this->nilai_bab5) {
            $total += $this->nilai_bab5;
            $count++;
        }
        if ($this->nilai_daftar_pustaka) {
            $total += $this->nilai_daftar_pustaka;
            $count++;
        }

        return $count > 0 ? round($total / $count, 2) : 0;
    }
}
