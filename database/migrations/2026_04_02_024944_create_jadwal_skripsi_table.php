<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_skripsi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftaran_id')->constrained('pendaftaran_skripsi')->onDelete('cascade');
            $table->date('tanggal');
            $table->time('waktu_mulai');
            $table->time('waktu_selesai');
            $table->string('ruang')->nullable();

            // Kolom untuk menyimpan nama dosen (teks)
            $table->string('dosen_penguji_1')->nullable();
            $table->string('dosen_penguji_2')->nullable();
            $table->string('dosen_penguji_3')->nullable();

            // Kolom untuk menyimpan ID dosen (relasi foreign key) - TANPA 'after'
            $table->foreignId('dosen_penguji_1_id')->nullable()->constrained('dosens')->onDelete('set null');
            $table->foreignId('dosen_penguji_2_id')->nullable()->constrained('dosens')->onDelete('set null');
            $table->foreignId('dosen_penguji_3_id')->nullable()->constrained('dosens')->onDelete('set null');

            $table->text('keterangan')->nullable();
            $table->enum('status', ['terjadwal', 'selesai', 'batal'])->default('terjadwal');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_skripsi');
    }
};
