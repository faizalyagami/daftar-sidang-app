<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penilaian_skripsi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_skripsi_id')->constrained('jadwal_skripsi')->onDelete('cascade');
            $table->foreignId('dosen_id')->nullable()->constrained('dosens')->onDelete('set null');
            $table->string('nama_dosen_penguji');
            $table->decimal('bobot', 5, 2)->default(0);
            $table->decimal('nilai', 5, 2)->nullable();
            $table->decimal('jumlah', 10, 2)->nullable();

            // Aspek penilaian individual
            $table->decimal('nilai_fenomena', 5, 2)->nullable();
            $table->decimal('nilai_variabel', 5, 2)->nullable();
            $table->decimal('nilai_teori_metode', 5, 2)->nullable();
            $table->decimal('nilai_alat_ukur', 5, 2)->nullable();
            $table->decimal('nilai_analisis', 5, 2)->nullable();
            $table->decimal('nilai_simpulan_saran', 5, 2)->nullable();
            $table->decimal('nilai_presentasi', 5, 2)->nullable();

            $table->decimal('nilai_akhir', 5, 2)->nullable();
            $table->text('catatan')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaian_skripsi');
    }
};
