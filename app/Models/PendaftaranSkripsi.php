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
        if ($this->allDocumentsValid()) {
            $this->update(['status' => 'approved']);
        } elseif ($this->reviewDetails()->where('status', 'reupload')->exists()) {
            $this->update(['status' => 'revision']);
        } else {
            $this->update(['status' => 'review']);
        }
    }

    public function academicPeriod() {
        return $this->belongsTo(AcademicPeriod::class);
    }
}