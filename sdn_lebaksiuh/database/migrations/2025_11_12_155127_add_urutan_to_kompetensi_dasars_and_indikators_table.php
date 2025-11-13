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
        Schema::table('kompetensi_dasars', function (Blueprint $table) {
            $table->integer('urutan')->nullable()->default(0)->after('mata_pelajaran_id');
        });

        Schema::table('indikators', function (Blueprint $table) {
            $table->integer('urutan')->nullable()->default(0)->after('mata_pelajaran_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kompetensi_dasars', function (Blueprint $table) {
            $table->dropColumn('urutan');
        });

        Schema::table('indikators', function (Blueprint $table) {
            $table->dropColumn('urutan');
        });
    }
};
