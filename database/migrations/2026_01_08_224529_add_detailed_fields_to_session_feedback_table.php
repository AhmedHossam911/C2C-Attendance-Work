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
        Schema::table('session_feedback', function (Blueprint $table) {
            // "How would you rate the clarity of the session’s objectives?" (1-5)
            $table->tinyInteger('objectives_clarity')->nullable();

            // "To what extent did you understand the instructors overall" (1-5)
            $table->tinyInteger('instructor_understanding')->nullable();

            // "Overall, how satisfied were you with the session?" (1-10)
            $table->tinyInteger('overall_satisfaction')->nullable();

            // "Did Room meet your needs for this session" (Definitely Yes...Definitely No)
            $table->string('room_suitability')->nullable();

            // "Rate Attendance System" (1-10)
            $table->tinyInteger('attendance_system_rating')->nullable();

            // "Do you Recommend Improvement related to Attendance System *" (Text)
            $table->text('attendance_system_suggestions')->nullable();

            // "Do you have any suggestions for improving future sessions?" (Text)
            $table->text('future_suggestions')->nullable();

            // Rename 'rating' to something more general or keep it? Original rating was 1-5. 
            // The user prompt has "Please share your thoughts about sessions performance... (1-10)" - WAIT, looking at user prompt:
            // "Please share your thoughts about sessions performance (positive or constructive feedback is welcome)" -> TEXT
            // "Overall, how satisfied were you with the session?" -> 1-10
            // The old 'rating' column (1-5) might be redundant or map to one of these. 
            // Let's keep existing columns and add new ones to avoid data loss if any exists, but primarily use new ones.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('session_feedback', function (Blueprint $table) {
            $table->dropColumn([
                'objectives_clarity',
                'instructor_understanding',
                'overall_satisfaction',
                'room_suitability',
                'attendance_system_rating',
                'attendance_system_suggestions',
                'future_suggestions',
            ]);
        });
    }
};
