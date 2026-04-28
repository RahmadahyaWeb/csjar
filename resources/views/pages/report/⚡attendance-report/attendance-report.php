<?php

// app/Livewire/Pages/AttendanceReport/Index.php

use App\Models\Attendance;
use App\Models\Department;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

new #[Title('Attendance Report')] class extends Component
{
    use WithoutUrlPagination, WithPagination;

    public $mode = 'daily';

    public $startDate;

    public $endDate;

    public $month;

    public $departmentId;

    public $userId;

    public function mount()
    {
        $this->startDate = now()->toDateString();
        $this->endDate = now()->toDateString();
        $this->month = now()->format('Y-m');
    }

    /**
     * Reset user ketika department berubah (DEPENDENT)
     */
    public function updatedDepartmentId()
    {
        $this->userId = null;
    }

    #[Computed]
    public function departments()
    {
        return Department::pluck('name', 'id');
    }

    /**
     * USER DEPEND ON DEPARTMENT
     */
    #[Computed]
    public function users()
    {
        return User::whereHas('employeeAssignment', function ($q) {
            if ($this->departmentId) {
                $q->where('department_id', $this->departmentId);
            }
        })->pluck('name', 'id');
    }

    #[Computed]
    public function reports()
    {
        $query = Attendance::with([
            'user.employeeAssignment.department',
            'user.employeeAssignment.position',
        ])
            ->when($this->departmentId, function ($q) {
                $q->whereHas('user.employeeAssignment', function ($sub) {
                    $sub->where('department_id', $this->departmentId);
                });
            })
            ->when($this->userId, fn ($q) => $q->where('user_id', $this->userId));

        if ($this->mode === 'daily') {

            return $query
                ->when($this->startDate, fn ($q) => $q->whereDate('date', '>=', $this->startDate))
                ->when($this->endDate, fn ($q) => $q->whereDate('date', '<=', $this->endDate))
                ->latest('date')
                ->paginate(10);
        }

        $start = Carbon::parse($this->month)->startOfMonth();
        $end = Carbon::parse($this->month)->endOfMonth();

        return $query
            ->whereBetween('date', [$start, $end])
            ->get()
            ->groupBy('user_id')
            ->map(function ($items) {

                return [
                    'user' => $items->first()->user,
                    'present' => $items->where('status', 'present')->count(),
                    'absent' => $items->where('status', 'absent')->count(),
                    'leave' => $items->where('status', 'leave')->count(),
                    'holiday' => $items->where('status', 'holiday')->count(),
                    'work_minutes' => $items->sum('work_minutes'),
                    'late_minutes' => $items->sum('late_minutes'),
                    'overtime_minutes' => $items->sum('overtime_minutes'),
                ];
            })->values();
    }
};
