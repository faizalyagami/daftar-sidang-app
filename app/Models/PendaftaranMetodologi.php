<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendaftaranMetodologi extends Model
{
    use HasFactory;

    protected $table = 'pendaftaran_metodologi';
    
    protected $fillable = [
        'mahasiswa_id',
        'email',
        'judul_penelitian',
        'dosen_pembimbing',
        'dosen_pembimbing_2',
        'kuliah_peminatan',
        'status',
        'reviewer_notes'
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }

    public function dokumen()
    {
        return $this->hasMany(DokumenMetodologi::class, 'pendaftaran_id');
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