<?php

// app/Models/WorkScheduleDay.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkScheduleDay extends Model
{
    protected $fillable = [
        'work_schedule_id',
        'day_of_week',
        'shift_id',
        'is_working_day',
    ];

    public function workSchedule()
    {
        return $this->belongsTo(WorkSchedule::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
}
