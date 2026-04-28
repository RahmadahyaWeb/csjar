<?php

// app/Livewire/Pages/AttendanceLateRanking/Index.php

use App\Models\Attendance;
use App\Models\Department;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Late Ranking')] class extends Component
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

                $totalLate = $rows->sum('late_minutes');

                return [
                    'user' => $rows->first()->user,
                    'department' => optional($rows->first()->user->employeeAssignment?->department)->name,
                    'total_late_minutes' => $totalLate,
                    'late_days' => $rows->where('late_minutes', '>', 0)->count(),
                ];
            })
            ->sortByDesc('total_late_minutes')
            ->values();
    }
};
