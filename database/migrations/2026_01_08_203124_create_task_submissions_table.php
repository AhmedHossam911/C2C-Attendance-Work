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
        Schema::create('task_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('submission_link')->nullable(); // Drive or Github
            $table->enum('status', ['pending', 'reviewed'])->default('pending');
            $table->integer('rating')->nullable(); // e.g. 0-100 or 1-10
            $table->text('feedback')->nullable();
            $table->boolean('is_late')->default(false);
            $table->timestamp('submitted_at')->useCurrent();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_submissions');
    }
};
