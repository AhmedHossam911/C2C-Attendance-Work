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
        // Change scanned_at to TIMESTAMP DEFAULT CURRENT_TIMESTAMP (removing ON UPDATE CURRENT_TIMESTAMP)
        DB::statement('ALTER TABLE attendance_records CHANGE scanned_at scanned_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original behavior (implicitly adding ON UPDATE if database defaults to it, or explicit)
        DB::statement('ALTER TABLE attendance_records CHANGE scanned_at scanned_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
    }
};
