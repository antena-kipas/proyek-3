<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE silabuses ALTER COLUMN id_tema TYPE INTEGER USING id_tema::integer');
        DB::statement('ALTER TABLE silabuses ALTER COLUMN id_subtema TYPE INTEGER USING id_subtema::integer');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('silabuses', function (Blueprint $table) {
            $table->string('id_tema')->change();
            $table->string('id_subtema')->change();
        });
    }
};
