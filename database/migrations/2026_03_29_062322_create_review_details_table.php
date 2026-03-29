<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('review_details', function (Blueprint $table) {
            $table->id();
            $table->morphs('pendaftaran');
            $table->foreignId('reviewer_id')->constrained('users')->onDelete('cascade');
            $table->string('jenis_dokumen');
            $table->enum('status', ['valid', 'invalid', 'reupload'])->default('valid');
            $table->text('komentar')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_details');
    }
};