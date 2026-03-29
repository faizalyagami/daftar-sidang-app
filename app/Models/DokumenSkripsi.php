<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DokumenSkripsi extends Model
{
    use HasFactory;

    protected $table = 'dokumen_skripsi';
    
    protected $fillable = ['pendaftaran_id', 'jenis_dokumen', 'file_path'];

    public function pendaftaran()
    {
        return $this->belongsTo(PendaftaranSkripsi::class, 'pendaftaran_id');
    }
}