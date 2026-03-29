<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pendaftaran_skripsi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained()->onDelete('cascade');
            $table->string('judul_skripsi');
            $table->string('dosen_pembimbing');
            $table->string('narasumber')->nullable();
            $table->date('tanggal_seminar')->nullable();
            $table->enum('status', ['pending', 'review', 'approved', 'rejected'])->default('pending');
            $table->text('reviewer_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pendaftaran_skripsi');
    }
};