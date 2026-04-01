<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pendaftaran_skripsi', function (Blueprint $table) {
            if (!Schema::hasColumn('pendaftaran_skripsi', 'academic_period_id')) {
                $table->foreignId('academic_period_id')->nullable()->after('mahasiswa_id')->constrained('academic_periods')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pendaftaran_skripsi', function (Blueprint $table) {
            Schema::table('pendaftaran_skripsi', function (Blueprint $table) {
                $table->dropForeign(['academic_period_id']);
                $table->dropColumn('academic_period_id');
            });
        });
    }
};
