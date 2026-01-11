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
        Schema::create('report_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('report_key'); // e.g., 'ghost_members'
            $table->string('role'); // e.g., 'hr'
            $table->boolean('can_view')->default(false);
            $table->timestamps();

            $table->unique(['report_key', 'role']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_permissions');
    }
};
