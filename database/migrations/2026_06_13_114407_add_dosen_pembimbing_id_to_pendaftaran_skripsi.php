<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pendaftaran_skripsi', function (Blueprint $table) {
            if (!Schema::hasColumn('pendaftaran_skripsi', 'dosen_pembimbing_id')) {
                $table->foreignId('dosen_pembimbing_id')->nullable()->after('dosen_pembimbing')->constrained('dosens')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pendaftaran_skripsi', function (Blueprint $table) {
            $table->dropForeign(['dosen_pembimbing_id']);
            $table->dropColumn('dosen_pembimbing_id');
        });
    }
};
