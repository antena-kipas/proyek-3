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
        Schema::table('rpps', function (Blueprint $table) {
            $table->dropColumn(['topik_materi', 'alokasi_waktu', 'tujuan_1', 'tujuan_2']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rpps', function (Blueprint $table) {
            $table->string('topik_materi');
            $table->string('alokasi_waktu');
            $table->text('tujuan_1');
            $table->text('tujuan_2')->nullable();
        });
    }
};
