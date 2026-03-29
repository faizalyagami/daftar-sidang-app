<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pendaftaran_metodologi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained()->onDelete('cascade');
            $table->string('email');
            $table->string('judul_penelitian');
            $table->string('dosen_pembimbing');
            $table->string('dosen_pembimbing_2')->nullable();
            $table->string('kuliah_peminatan');
            $table->enum('status', ['pending', 'review', 'approved', 'rejected'])->default('pending');
            $table->text('reviewer_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pendaftaran_metodologi');
    }
};