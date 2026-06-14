<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('feedback_skripsi', function (Blueprint $table) {
            if (!Schema::hasColumn('feedback_skripsi', 'dosen_id')) {
                $table->foreignId('dosen_id')->nullable()->after('id')->constrained('dosens')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('feedback_skripsi', function (Blueprint $table) {
            $table->dropForeign(['dosen_id']);
            $table->dropColumn('dosen_id');
        });
    }
};
