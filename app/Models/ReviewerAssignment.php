<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewerAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'reviewer_id',
        'pendaftaran_id',
        'pendaftaran_type',
        'queue_number',
        'status',
        'assigned_at',
        'started_at',
        'completed_at'
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime'
    ];

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function pendaftaran()
    {
        if ($this->pendaftaran_type == 'skripsi') {
            return $this->belongsTo(PendaftaranSkripsi::class, 'pendaftaran_id');
        }
        return $this->belongsTo(PendaftaranMetodologi::class, 'pendaftaran_id');
    }
}