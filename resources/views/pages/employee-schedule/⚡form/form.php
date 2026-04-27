<?php

use App\Models\EmployeeSchedule;
use App\Models\User;
use App\Models\WorkSchedule;
use App\Traits\AuthorizesCrud;
use Flux\Flux;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

new class extends Component
{
    use AuthorizesCrud, AuthorizesRequests;

    public ?EmployeeSchedule $schedule = null;

    public $user_id;

    public $work_schedule_id;

    public $start_date;

    public $end_date;

    public $is_active = true;

    public $users = [];

    public $schedules = [];

    public function mount(?EmployeeSchedule $schedule = null)
    {
        $this->users = User::pluck('name', 'id');
        $this->schedules = WorkSchedule::pluck('name', 'id');

        if ($schedule && $schedule->exists) {
            $this->authorizeUpdate($schedule);

            $this->schedule = $schedule;

            $this->user_id = $schedule->user_id;
            $this->work_schedule_id = $schedule->work_schedule_id;
            $this->start_date = $schedule->start_date;
            $this->end_date = $schedule->end_date;
            $this->is_active = $schedule->is_active;
        } else {
            $this->authorizeStore(EmployeeSchedule::class);

            $this->start_date = now()->format('Y-m-d');
        }
    }

    public function save()
    {
        $this->validate([
            'user_id' => ['required', 'exists:users,id'],
            'work_schedule_id' => ['required', 'exists:work_schedules,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'is_active' => ['required', 'boolean'],
        ]);

        return $this->transaction(function () {

            $payload = [
                'user_id' => $this->user_id,
                'work_schedule_id' => $this->work_schedule_id,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'is_active' => $this->is_active,
            ];

            if ($this->employeeSchedule) {
                $this->authorizeUpdate($this->employeeSchedule);

                $this->employeeSchedule->update($payload);

                Flux::toast(
                    heading: 'Success',
                    text: 'Employee schedule updated successfully',
                    variant: 'success'
                );
            } else {
                $this->authorizeStore(EmployeeSchedule::class);

                EmployeeSchedule::create($payload);

                Flux::toast(
                    heading: 'Success',
                    text: 'Employee schedule created successfully',
                    variant: 'success'
                );
            }

            $this->redirect(route('employee-schedules.index'), navigate: true);
        });
    }
};
