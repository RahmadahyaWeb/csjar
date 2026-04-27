<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BreakLog extends Model
{
    protected $fillable = [
        'user_id',
        'shift_id',
        'break_rule_id',
        'start_at',
        'end_at',
        'duration_minutes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function rule()
    {
        return $this->belongsTo(BreakRule::class, 'break_rule_id');
    }
}
