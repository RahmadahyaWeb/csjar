<?php

// app/Models/Attendance.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'status',
        'checkin_at',
        'checkout_at',
        'work_minutes',
        'break_minutes',
        'late_minutes',
        'early_leave_minutes',
        'overtime_minutes',
        'overtime_status',
        'overtime_approved_by',
        'overtime_approved_at',
    ];

    protected $casts = [
        'date' => 'date',
        'checkin_at' => 'datetime',
        'checkout_at' => 'datetime',
        'overtime_approved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
