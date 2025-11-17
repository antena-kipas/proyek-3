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
        Schema::table('materi_pelajarans', function (Blueprint $table) {
            $table->dropColumn(['judul', 'konten']);
            $table->text('materi_pelajaran');
        });

        Schema::table('kegiatan_pembelajarans', function (Blueprint $table) {
            $table->dropColumn(['judul', 'deskripsi']);
            $table->text('kegiatan_pembelajaran');
        });

        Schema::table('penilaian_diris', function (Blueprint $table) {
            $table->dropColumn(['pertanyaan', 'jawaban']);
            $table->text('penilaian_diri');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('materi_pelajarans', function (Blueprint $table) {
            $table->string('judul');
            $table->text('konten');
            $table->dropColumn('materi_pelajaran');
        });

        Schema::table('kegiatan_pembelajarans', function (Blueprint $table) {
            $table->string('judul');
            $table->text('deskripsi');
            $table->dropColumn('kegiatan_pembelajaran');
        });

        Schema::table('penilaian_diris', function (Blueprint $table) {
            $table->text('pertanyaan');
            $table->text('jawaban')->nullable();
            $table->dropColumn('penilaian_diri');
        });
    }
};