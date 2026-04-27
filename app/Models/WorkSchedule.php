<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkSchedule extends Model
{
    protected $fillable = [
        'name',
        'code',
    ];

    public function days()
    {
        return $this->hasMany(WorkScheduleDay::class);
    }
}
