<?php

use App\Models\Shift;
use App\Models\WorkSchedule;
use App\Models\WorkScheduleDay;
use App\Traits\AuthorizesCrud;
use Flux\Flux;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

new class extends Component
{
    use AuthorizesCrud, AuthorizesRequests;

    public ?WorkSchedule $schedule = null;

    public $name;

    public $code;

    public $days = [];

    public $shifts = [];

    public function mount(?WorkSchedule $schedule = null)
    {
        $this->shifts = Shift::pluck('name', 'id');

        // init default 7 hari
        $this->days = collect(range(0, 6))->mapWithKeys(fn ($day) => [
            $day => [
                'is_working_day' => true,
                'shift_id' => null,
            ],
        ])->toArray();

        if ($schedule && $schedule->exists) {
            $this->authorizeUpdate($schedule);

            $this->schedule = $schedule;

            $this->name = $schedule->name;
            $this->code = $schedule->code;

            foreach ($schedule->days as $day) {
                $this->days[$day->day_of_week] = [
                    'is_working_day' => $day->is_working_day,
                    'shift_id' => $day->shift_id,
                ];
            }
        } else {
            $this->authorizeStore(WorkSchedule::class);
        }
    }

    public function save()
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:50',
                $this->schedule
                    ? 'unique:work_schedules,code,'.$this->schedule->id
                    : 'unique:work_schedules,code',
            ],
            'days' => ['required', 'array'],
        ]);

        return $this->transaction(function () {

            if ($this->schedule) {
                $this->authorizeUpdate($this->schedule);

                $this->schedule->update([
                    'name' => $this->name,
                    'code' => $this->code,
                ]);

                $schedule = $this->schedule;
            } else {
                $this->authorizeStore(WorkSchedule::class);

                $schedule = WorkSchedule::create([
                    'name' => $this->name,
                    'code' => $this->code,
                ]);
            }

            // sync days
            WorkScheduleDay::where('work_schedule_id', $schedule->id)->delete();

            foreach ($this->days as $day => $config) {
                WorkScheduleDay::create([
                    'work_schedule_id' => $schedule->id,
                    'day_of_week' => $day,
                    'is_working_day' => $config['is_working_day'],
                    'shift_id' => $config['is_working_day'] ? $config['shift_id'] : null,
                ]);
            }

            Flux::toast(
                heading: 'Success',
                text: $this->schedule
                    ? 'Work schedule updated successfully'
                    : 'Work schedule created successfully',
                variant: 'success'
            );

            $this->redirect(route('work-schedules.index'), navigate: true);
        });
    }

    public function getDayLabel($day)
    {
        return [
            0 => 'Sunday',
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
        ][$day] ?? '-';
    }
};
