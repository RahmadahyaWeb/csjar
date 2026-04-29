<?php

// app/Livewire/Pages/EmployeeScheduleReport/Index.php

use App\Models\Department;
use App\Models\EmployeeSchedule;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Employee Schedule Report')] class extends Component
{
    public $departmentId;

    public $userId;

    public $month;

    public function mount()
    {
        $this->month = now()->format('Y-m');
    }

    #[Computed]
    public function departments()
    {
        return Department::orderBy('name')->get();
    }

    #[Computed]
    public function users()
    {
        return User::when($this->departmentId, function ($q) {
            $q->whereHas('employeeAssignment', function ($sub) {
                $sub->where('department_id', $this->departmentId);
            });
        })
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function schedules()
    {
        $start = Carbon::parse($this->month)->startOfMonth();
        $end = Carbon::parse($this->month)->endOfMonth();

        return EmployeeSchedule::with([
            'user.employeeAssignment.department',
            'workSchedule.days.shift',
        ])
            ->when($this->departmentId, function ($q) {
                $q->whereHas('user.employeeAssignment', function ($sub) {
                    $sub->where('department_id', $this->departmentId);
                });
            })
            ->when($this->userId, fn ($q) => $q->where('user_id', $this->userId))
            ->whereDate('start_date', '<=', $end)
            ->where(function ($q) use ($start) {
                $q->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', $start);
            })
            ->get();
    }
};
