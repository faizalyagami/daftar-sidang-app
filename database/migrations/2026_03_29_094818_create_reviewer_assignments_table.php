<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel untuk mencatat assignment reviewer
        Schema::create('reviewer_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reviewer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('pendaftaran_id');
            $table->string('pendaftaran_type'); // 'skripsi' or 'metodologi'
            $table->integer('queue_number')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
        
        // Tambahkan kolom di pendaftaran_skripsi
        Schema::table('pendaftaran_skripsi', function (Blueprint $table) {
            $table->foreignId('reviewer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('assigned_at')->nullable();
        });
        
        // Tambahkan kolom di pendaftaran_metodologi
        Schema::table('pendaftaran_metodologi', function (Blueprint $table) {
            $table->foreignId('reviewer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('assigned_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviewer_assignments');
        
        Schema::table('pendaftaran_skripsi', function (Blueprint $table) {
            $table->dropForeign(['reviewer_id']);
            $table->dropColumn(['reviewer_id', 'assigned_at']);
        });
        
        Schema::table('pendaftaran_metodologi', function (Blueprint $table) {
            $table->dropForeign(['reviewer_id']);
            $table->dropColumn(['reviewer_id', 'assigned_at']);
        });
    }
};