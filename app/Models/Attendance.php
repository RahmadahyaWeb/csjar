<?php

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
    ];

    protected $casts = [
        'date' => 'date',
        'checkin_at' => 'datetime',
        'checkout_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
