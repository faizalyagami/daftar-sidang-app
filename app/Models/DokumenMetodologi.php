<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DokumenMetodologi extends Model
{
    use HasFactory;

    protected $table = 'dokumen_metodologi'; 
    
    protected $fillable = ['pendaftaran_id', 'jenis_dokumen', 'file_path'];

    public function pendaftaran()
    {
        return $this->belongsTo(PendaftaranMetodologi::class, 'pendaftaran_id');
    }
}