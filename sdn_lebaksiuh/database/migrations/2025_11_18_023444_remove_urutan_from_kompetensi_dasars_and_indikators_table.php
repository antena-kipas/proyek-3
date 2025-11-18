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
            $table->dropColumn('urutan');
        });

        Schema::table('indikators', function (Blueprint $table) {
            $table->dropColumn('urutan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kompetensi_dasars', function (Blueprint $table) {
            $table->integer('urutan')->after('deskripsi');
        });

        Schema::table('indikators', function (Blueprint $table) {
            $table->integer('urutan')->after('deskripsi');
        });
    }
};