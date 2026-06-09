<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rekapitulasi_nilai_skripsi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_skripsi_id')->constrained('jadwal_skripsi')->onDelete('cascade');
            $table->decimal('nilai_akhir', 5, 2)->nullable();
            $table->string('huruf_mutu')->nullable();
            $table->text('catatan_kumulatif')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rekapitulasi_nilai_skripsi');
    }
};
