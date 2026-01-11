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
        Schema::table('report_permissions', function (Blueprint $table) {
            $table->string('access_level')->default('none')->after('role'); // none, own, global
            $table->dropColumn('can_view');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('report_permissions', function (Blueprint $table) {
            $table->boolean('can_view')->default(false);
            $table->dropColumn('access_level');
        });
    }
};
