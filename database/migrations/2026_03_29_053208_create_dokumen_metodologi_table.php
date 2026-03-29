<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dokumen_metodologi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftaran_id')->constrained('pendaftaran_metodologi')->onDelete('cascade');
            $table->enum('jenis_dokumen', [
                'proposal_word',
                'kartu_bimbingan',
                'surat_ijin_ujian',
                'lembar_pengesahan'
            ]);
            $table->string('file_path');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokumen_metodologi');
    }
};