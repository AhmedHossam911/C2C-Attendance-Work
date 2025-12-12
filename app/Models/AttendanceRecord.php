<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttendanceRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'attendance_session_id',
        'user_id',
        'scanned_by',
        'scanned_at',
        'status',
        'notes',
        'updated_by',
    ];

    protected $casts = [
        'scanned_at' => 'datetime',
    ];

    public function session()
    {
        return $this->belongsTo(AttendanceSession::class, 'attendance_session_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scanner()
    {
        return $this->belongsTo(User::class, 'scanned_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
