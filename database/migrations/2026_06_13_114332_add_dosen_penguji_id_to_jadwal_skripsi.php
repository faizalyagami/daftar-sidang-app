<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jadwal_skripsi', function (Blueprint $table) {
            if (!Schema::hasColumn('jadwal_skripsi', 'dosen_penguji_1_id')) {
                $table->foreignId('dosen_penguji_1_id')->nullable()->after('dosen_penguji_1')->constrained('dosens')->onDelete('set null');
            }
            if (!Schema::hasColumn('jadwal_skripsi', 'dosen_penguji_2_id')) {
                $table->foreignId('dosen_penguji_2_id')->nullable()->after('dosen_penguji_2')->constrained('dosens')->onDelete('set null');
            }
            if (!Schema::hasColumn('jadwal_skripsi', 'dosen_penguji_3_id')) {
                $table->foreignId('dosen_penguji_3_id')->nullable()->after('dosen_penguji_3')->constrained('dosens')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('jadwal_skripsi', function (Blueprint $table) {
            $table->dropForeign(['dosen_penguji_1_id']);
            $table->dropForeign(['dosen_penguji_2_id']);
            $table->dropForeign(['dosen_penguji_3_id']);
            $table->dropColumn(['dosen_penguji_1_id', 'dosen_penguji_2_id', 'dosen_penguji_3_id']);
        });
    }
};
