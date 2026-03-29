<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cek dan tambahkan kolom ke tabel pendaftaran_skripsi jika belum ada
        if (!Schema::hasColumn('pendaftaran_skripsi', 'reviewer_id')) {
            Schema::table('pendaftaran_skripsi', function (Blueprint $table) {
                $table->foreignId('reviewer_id')->nullable()->constrained('users')->onDelete('set null');
            });
        }
        
        if (!Schema::hasColumn('pendaftaran_skripsi', 'assigned_at')) {
            Schema::table('pendaftaran_skripsi', function (Blueprint $table) {
                $table->timestamp('assigned_at')->nullable();
            });
        }
        
        // Cek dan tambahkan kolom ke tabel pendaftaran_metodologi jika belum ada
        if (!Schema::hasColumn('pendaftaran_metodologi', 'reviewer_id')) {
            Schema::table('pendaftaran_metodologi', function (Blueprint $table) {
                $table->foreignId('reviewer_id')->nullable()->constrained('users')->onDelete('set null');
            });
        }
        
        if (!Schema::hasColumn('pendaftaran_metodologi', 'assigned_at')) {
            Schema::table('pendaftaran_metodologi', function (Blueprint $table) {
                $table->timestamp('assigned_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        // Hapus kolom dari tabel pendaftaran_skripsi
        if (Schema::hasColumn('pendaftaran_skripsi', 'reviewer_id')) {
            Schema::table('pendaftaran_skripsi', function (Blueprint $table) {
                $table->dropForeign(['reviewer_id']);
                $table->dropColumn('reviewer_id');
            });
        }
        
        if (Schema::hasColumn('pendaftaran_skripsi', 'assigned_at')) {
            Schema::table('pendaftaran_skripsi', function (Blueprint $table) {
                $table->dropColumn('assigned_at');
            });
        }
        
        // Hapus kolom dari tabel pendaftaran_metodologi
        if (Schema::hasColumn('pendaftaran_metodologi', 'reviewer_id')) {
            Schema::table('pendaftaran_metodologi', function (Blueprint $table) {
                $table->dropForeign(['reviewer_id']);
                $table->dropColumn('reviewer_id');
            });
        }
        
        if (Schema::hasColumn('pendaftaran_metodologi', 'assigned_at')) {
            Schema::table('pendaftaran_metodologi', function (Blueprint $table) {
                $table->dropColumn('assigned_at');
            });
        }
    }
};