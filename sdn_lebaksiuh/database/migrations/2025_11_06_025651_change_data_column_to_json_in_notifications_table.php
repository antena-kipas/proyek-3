<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Using raw statement for PostgreSQL compatibility to change TEXT to JSONB
        DB::statement('ALTER TABLE notifications ALTER COLUMN data TYPE JSONB USING data::text::jsonb');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert JSONB back to TEXT if needed
        DB::statement('ALTER TABLE notifications ALTER COLUMN data TYPE TEXT USING data::jsonb::text');
    }
};