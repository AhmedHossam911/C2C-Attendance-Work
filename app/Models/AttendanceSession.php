<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'status',
        'late_threshold_minutes',
        'counts_for_attendance',
        'created_by',
        'committee_id',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function committee()
    {
        return $this->belongsTo(Committee::class);
    }

    public function records()
    {
        return $this->hasMany(AttendanceRecord::class);
    }
}
