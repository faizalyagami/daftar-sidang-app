<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'pendaftaran_id',
        'pendaftaran_type',
        'reviewer_id',
        'jenis_dokumen',
        'status',
        'komentar'
    ];

    public function pendaftaran()
    {
        return $this->morphTo();
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
    
    // Status options
    const STATUS_VALID = 'valid';
    const STATUS_INVALID = 'invalid';
    const STATUS_REUPLOAD = 'reupload';
}