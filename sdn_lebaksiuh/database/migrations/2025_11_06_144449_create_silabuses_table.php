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
        Schema::create('silabuses', function (Blueprint $table) {
            $table->id();
            $table->string('tema');
            $table->string('id_tema');
            $table->string('subtema');
            $table->string('id_subtema');
            $table->enum('semester', [1, 2]);
            $table->timestamps();
        });

        Schema::create('kompetensi_intis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('silabus_id')->constrained('silabuses')->onDelete('cascade');
            $table->integer('urutan');
            $table->text('kompetensi_inti');
            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kompetensi_intis');
        Schema::dropIfExists('silabuses');
    }
};
