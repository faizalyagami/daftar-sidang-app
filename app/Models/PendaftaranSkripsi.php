<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendaftaranSkripsi extends Model
{
    use HasFactory;

    protected $table = 'pendaftaran_skripsi';

    protected $fillable = [
        'mahasiswa_id',
        'academic_period_id',
        'judul_skripsi',
        'dosen_pembimbing',
        'narasumber',
        'tanggal_seminar',
        'status',
        'reviewer_notes',
        'reviewer_id',
        'assigned_at'
    ];

    protected $casts = [
        'tanggal_seminar' => 'date',
        'assigned_at' => 'datetime'
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function dokumen()
    {
        return $this->hasMany(DokumenSkripsi::class, 'pendaftaran_id');
    }

    public function reviewDetails()
    {
        return $this->morphMany(ReviewDetail::class, 'pendaftaran');
    }

    public function allDocumentsValid()
    {
        $invalidDocs = $this->reviewDetails()
            ->where('status', '!=', 'valid')
            ->count();

        return $invalidDocs == 0;
    }

    public function updateStatusFromReviews()
    {
        $hasReupload = $this->reviewDetails()->where('status', 'reupload')->exists();
        $hasInvalid = $this->reviewDetails()->where('status', 'invalid')->exists();
        $allValid = !$hasReupload && !$hasInvalid;

        if ($allValid) {
            // Semua dokumen valid, lanjut sidang
            $this->update(['status' => 'approved']);
        } elseif ($hasReupload) {
            // Ada yang perlu upload ulang, status revision
            $this->update(['status' => 'revision']);
        } else {
            // Ada yang invalid (tapi tidak perlu upload ulang)
            $this->update(['status' => 'rejected']);
        }
    }

    public function academicPeriod()
    {
        return $this->belongsTo(AcademicPeriod::class);
    }

    public function jadwal()
    {
        return $this->hasOne(JadwalSkripsi::class, 'pendaftaran_id');
    }

    public function getDokumenYangPerluDirevisi()
    {
        return $this->reviewDetails()
            ->where('status', 'reupload')
            ->get();
    }

    public function getCatatanRevisi()
    {
        $revisi = $this->reviewDetails()
            ->where('status', 'reupload')
            ->get();

        $catatan = [];
        foreach ($revisi as $r) {
            $namaDokumen = $this->getNamaDokumen($r->jenis_dokumen);
            $catatan[] = [
                'dokumen' => $namaDokumen,
                'komentar' => $r->komentar
            ];
        }
        return $catatan;
    }

    private function getNamaDokumen($jenis)
    {
        $list = [
            'bukti_pembayaran_registrasi' => 'Bukti Pembayaran Registrasi Terakhir',
            'bukti_pembayaran_sidang' => 'Bukti Pembayaran Sidang',
            'bukti_pembayaran_skripsi' => 'Bukti Pembayaran Skripsi',
            'frs' => 'Formulir Rencana Studi (FRS)',
            'transkrip_nilai' => 'Transkrip Nilai',
            'surat_bebas_perpus' => 'Surat Bebas Perpustakaan',
            'surat_bebas_alat_tes' => 'Surat Bebas Alat Tes',
            'sertifikat_pesantren' => 'Sertifikat Pesantren',
            'sertifikat_sks_non_akademik' => 'Sertifikat SKS Non Akademik',
            'surat_lolos_turnitin' => 'Surat Lolos Turnitin',
            'sertifikat_toefl' => 'Sertifikat TOEFL',
            'pas_foto' => 'Pas Foto',
            'buku_bimbingan' => 'Buku Bimbingan',
            'surat_perbaikan' => 'Surat Perbaikan',
            'surat_ijin_sidang' => 'Surat Ijin Sidang',
            'berkas_skripsi' => 'Berkas Skripsi'
        ];
        return $list[$jenis] ?? $jenis;
    }
}
