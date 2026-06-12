<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback_skripsi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_skripsi_id')->constrained('jadwal_skripsi')->onDelete('cascade');
            $table->foreignId('dosen_id')->nullable()->constrained('dosens')->onDelete('set null');
            $table->string('nama_dosen');

            // Nilai untuk setiap bagian
            $table->integer('nilai_bagian_depan')->nullable();
            $table->integer('nilai_bab1')->nullable();
            $table->integer('nilai_bab2')->nullable();
            $table->integer('nilai_bab3')->nullable();
            $table->integer('nilai_bab4')->nullable();
            $table->integer('nilai_bab5')->nullable();
            $table->integer('nilai_daftar_pustaka')->nullable();

            // Rekomendasi
            $table->enum('rekomendasi', ['layak', 'perbaikan_minor', 'perbaikan_mayor', 'tidak_layak'])->nullable();
            $table->text('catatan_perbaikan')->nullable();
            $table->boolean('perbaikan_mayor')->default(false);
            $table->boolean('perbaikan_minor')->default(false);
            $table->string('file_catatan')->nullable();

            $table->boolean('is_completed')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback_skripsi');
    }
};
