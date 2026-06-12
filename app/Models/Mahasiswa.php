<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'npm',
        'tempat_lahir',
        'tanggal_lahir',
        'ipk',
        'no_hp',
        'dosen_wali'
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'ipk' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function skripsiTerbaru()
    {
        return $this->pendaftaranSkripsi()->latest()->first();
    }

    public function metodologiTerbaru()
    {
        return $this->pendaftaranMetodologi()->latest()->first();
    }

    public function getSkripsiStatus()
    {
        $data = $this->skripsiTerbaru();

        if (!$data || $data->status == 'belum_daftar') {
            return ['text' => 'Belum Mendaftar', 'class' => 'status-badge-pending'];
        }

        return match ($data->status) {
            'approved' => ['text' => 'Disetujui', 'class' => 'status-badge-approved'],
            'review' => ['text' => 'Review', 'class' => 'status-badge-review'],
            'rejected' => ['text' => 'Ditolak', 'class' => 'status-badge-rejected'],
            'revision' => ['text' => 'Revisi', 'class' => 'status-badge-revision'],
            default => ['text' => 'Sudah Mendaftar', 'class' => 'status-badge-approved'],
        };
    }

    public function getMetodologiStatus()
    {
        $data = $this->metodologiTerbaru();

        if (!$data || $data->status == 'belum_daftar') {
            return ['text' => 'Belum Mendaftar', 'class' => 'status-badge-pending'];
        }

        return match ($data->status) {
            'approved' => ['text' => 'Disetujui', 'class' => 'status-badge-approved'],
            'review' => ['text' => 'Review', 'class' => 'status-badge-review'],
            'rejected' => ['text' => 'Ditolak', 'class' => 'status-badge-rejected'],
            default => ['text' => 'Sudah Mendaftar', 'class' => 'status-badge-approved'],
        };
    }

    public function pendaftaranSkripsi()
    {
        return $this->hasMany(PendaftaranSkripsi::class);
    }

    public function pendaftaranMetodologi()
    {
        return $this->hasMany(PendaftaranMetodologi::class);
    }

    public function getSkripsiDuration()
    {
        $first = $this->pendaftaranSkripsi()->orderBy('created_at', 'asc')->first();
        if (!$first) return null;

        $last = $this->pendaftaranSkripsi()->orderBy('created_at', 'desc')->first();
        $start = $first->created_at;
        $end = $last->created_at ?? now();

        $diff = $start->diff($end);
        $years = $diff->y;
        $months = $diff->m;

        return "{$years} tahun {$months} bulan";
    }

    public function getMetodologiDuration()
    {
        $first = $this->pendaftaranMetodologi()->orderBy('created_at', 'asc')->first();
        if (!$first) return null;

        $last = $this->pendaftaranMetodologi()->orderBy('created_at', 'desc')->first();
        $start = $first->created_at;
        $end = $last->created_at ?? now();

        $diff = $start->diff($end);
        $years = $diff->y;
        $months = $diff->m;

        return "{$years} tahun {$months} bulan";
    }

    public function getSkripsiPeriods()
    {
        return $this->pendaftaranSkripsi()
            ->with('academicPeriod')
            ->orderBy('created_at')
            ->get()
            ->unique(function ($item) {
                return $item->academicPeriod ? $item->academicPeriod->id : null;
            })
            ->map(function ($p) {
                return $p->academicPeriod ? $p->academicPeriod->semester . ' ' . $p->academicPeriod->tahun_akademik : 'Periode tidak diketahui';
            });
    }

    public function getMetodologiPeriods()
    {
        return $this->pendaftaranMetodologi()
            ->with('academicPeriod')
            ->orderBy('created_at')
            ->get()
            ->unique(function ($item) {
                return $item->academicPeriod ? $item->academicPeriod->id : null;
            })
            ->map(function ($p) {
                return $p->academicPeriod ? $p->academicPeriod->semester . ' ' . $p->academicPeriod->tahun_akademik : 'Periode tidak diketahui';
            });
    }

    public function getFeedbackSkripsi()
    {
        $pendaftaran = $this->pendaftaranSkripsi()
            ->where('status', 'approved')
            ->latest()
            ->first();

        if (!$pendaftaran || !$pendaftaran->jadwal) {
            return null;
        }

        $feedback = $pendaftaran->jadwal->feedback()->first();
        return $feedback;
    }

    public function getFeedbackMetodologi()
    {
        $pendaftaran = $this->pendaftaranMetodologi()
            ->where('status', 'approved')
            ->latest()
            ->first();

        if (!$pendaftaran || !$pendaftaran->jadwal) {
            return null;
        }

        $feedback = $pendaftaran->jadwal->feedback()->first();
        return $feedback;
    }

    public function getNilaiAkhirSkripsi()
    {
        $pendaftaran = $this->pendaftaranSkripsi()
            ->where('status', 'approved')
            ->latest()
            ->first();

        if (!$pendaftaran || !$pendaftaran->jadwal) {
            return null;
        }

        $rekapitulasi = $pendaftaran->jadwal->rekapitulasi()->first();
        return $rekapitulasi;
    }

    public function getNilaiAkhirMetodologi()
    {
        $pendaftaran = $this->pendaftaranMetodologi()
            ->where('status', 'approved')
            ->latest()
            ->first();

        if (!$pendaftaran || !$pendaftaran->jadwal) {
            return null;
        }

        $rekapitulasi = $pendaftaran->jadwal->rekapitulasi()->first();
        return $rekapitulasi;
    }
}
