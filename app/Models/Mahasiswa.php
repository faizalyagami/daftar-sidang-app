<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'npm', 'tempat_lahir', 'tanggal_lahir', 
        'ipk', 'no_hp', 'dosen_wali'
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'ipk' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pendaftaranSkripsi()
    {
        return $this->hasMany(PendaftaranSkripsi::class);
    }

    public function pendaftaranMetodologi()
    {
        return $this->hasMany(PendaftaranMetodologi::class);
    }
}