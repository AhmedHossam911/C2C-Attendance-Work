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
        // Only run on MySQL - SQLite uses string type for role and doesn't need ENUM modification
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('top_management', 'board', 'hr', 'member', 'committee_head') DEFAULT 'member'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only run on MySQL - SQLite uses string type for role and doesn't need ENUM modification
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('top_management', 'board', 'hr', 'member') DEFAULT 'member'");
        }
    }
};
