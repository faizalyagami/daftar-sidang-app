<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('feedback_skripsi', function (Blueprint $table) {
            $table->text('catatan_bagian_depan')->nullable()->after('catatan_perbaikan');
            $table->text('catatan_bab1')->nullable()->after('catatan_bagian_depan');
            $table->text('catatan_bab2')->nullable()->after('catatan_bab1');
            $table->text('catatan_bab3')->nullable()->after('catatan_bab2');
            $table->text('catatan_bab4')->nullable()->after('catatan_bab3');
            $table->text('catatan_bab5')->nullable()->after('catatan_bab4');
            $table->text('catatan_daftar_pustaka')->nullable()->after('catatan_bab5');
            $table->text('catatan_presentasi')->nullable()->after('catatan_daftar_pustaka');
        });
    }

    public function down(): void
    {
        Schema::table('feedback_skripsi', function (Blueprint $table) {
            $table->dropColumn([
                'catatan_bagian_depan',
                'catatan_bab1',
                'catatan_bab2',
                'catatan_bab3',
                'catatan_bab4',
                'catatan_bab5',
                'catatan_daftar_pustaka',
                'catatan_presentasi'
            ]);
        });
    }
};
