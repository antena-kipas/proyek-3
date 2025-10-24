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
            $table->tinyInteger('semester')->after('user_id');
            $table->tinyInteger('tema_id')->after('semester');
            $table->string('tema_name')->after('tema_id');
            $table->tinyInteger('sub_tema_id')->after('tema_name');
            $table->string('sub_tema_name')->after('sub_tema_id');
            $table->tinyInteger('pembelajaran_ke')->after('sub_tema_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rpps', function (Blueprint $table) {
            $table->dropColumn(['semester', 'tema_id', 'tema_name', 'sub_tema_id', 'sub_tema_name', 'pembelajaran_ke']);
            $table->text('content')->nullable();
        });
    }
};