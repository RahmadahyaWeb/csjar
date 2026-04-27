<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BreakRule extends Model
{
    protected $fillable = [
        'shift_id',
        'name',
        'start_time',
        'end_time',
        'duration_minutes',
        'is_paid',
        'is_flexible',
    ];

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
}
