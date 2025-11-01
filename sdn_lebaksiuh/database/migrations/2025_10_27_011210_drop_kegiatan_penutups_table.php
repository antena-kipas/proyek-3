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
        Schema::dropIfExists('kegiatan_penutups');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We are not recreating the table on rollback for this case.
    }
};
