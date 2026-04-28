<?php

// app/Livewire/Pages/AttendanceOvertime/Index.php

use App\Models\Attendance;
use App\Models\Department;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Overtime Report')] class extends Component
{
    public $startDate;

    public $endDate;

    public $departmentId;

    public function mount()
    {
        $this->startDate = now()->startOfMonth()->toDateString();
        $this->endDate = now()->endOfMonth()->toDateString();
    }

    #[Computed]
    public function departments()
    {
        return Department::orderBy('name')->get();
    }

    #[Computed]
    public function summary()
    {
        $query = Attendance::query()
            ->whereBetween('date', [$this->startDate, $this->endDate])
            ->where('status', 'present');

        if ($this->departmentId) {
            $query->whereHas('user.employeeAssignment', function ($q) {
                $q->where('department_id', $this->departmentId);
            });
        }

        return [
            'total_employee' => (clone $query)->where('overtime_minutes', '>', 0)->distinct('user_id')->count('user_id'),
            'total_overtime_minutes' => (clone $query)->sum('overtime_minutes'),
            'total_overtime_days' => (clone $query)->where('overtime_minutes', '>', 0)->count(),
        ];
    }

    #[Computed]
    public function rankings()
    {
        return Attendance::with('user.employeeAssignment.department')
            ->whereBetween('date', [$this->startDate, $this->endDate])
            ->where('status', 'present')
            ->when($this->departmentId, function ($q) {
                $q->whereHas('user.employeeAssignment', function ($sub) {
                    $sub->where('department_id', $this->departmentId);
                });
            })
            ->get()
            ->groupBy('user_id')
            ->map(function ($rows) {

                $totalOvertime = $rows->sum('overtime_minutes');

                return [
                    'user' => $rows->first()->user,
                    'department' => optional($rows->first()->user->employeeAssignment?->department)->name,
                    'overtime_days' => $rows->where('overtime_minutes', '>', 0)->count(),
                    'total_overtime_minutes' => $totalOvertime,
                ];
            })
            ->filter(fn ($item) => $item['total_overtime_minutes'] > 0)
            ->sortByDesc('total_overtime_minutes')
            ->values();
    }
};
