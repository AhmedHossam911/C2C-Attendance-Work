<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SessionFeedback extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_session_id',
        'user_id',
        'rating', // Keeping backward compatibility if needed, though strictly we might not use it in new form
        'feedback', // "Please share your thoughts about sessions performance..."

        // New Fields
        'objectives_clarity',
        'instructor_understanding',
        'overall_satisfaction',
        'room_suitability',
        'attendance_system_rating',
        'attendance_system_suggestions',
        'future_suggestions',
    ];

    public function session()
    {
        return $this->belongsTo(AttendanceSession::class, 'attendance_session_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
