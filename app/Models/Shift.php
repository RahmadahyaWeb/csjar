<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    protected $fillable = [
        'name',
        'code',
        'start_time',
        'end_time',
        'is_overnight',
        'tolerance_late',
        'tolerance_early_leave',
    ];

    public function getIsOvernightAttribute(): bool
    {
        return $this->start_time > $this->end_time;
    }
}
