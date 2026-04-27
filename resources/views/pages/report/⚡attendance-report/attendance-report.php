<?php

// app/Livewire/Pages/AttendanceReport/Index.php

use App\Models\Attendance;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

new #[Title('Attendance Report')] class extends Component
{
    use WithoutUrlPagination, WithPagination;

    public $mode = 'daily'; // daily | monthly

    public $startDate;

    public $endDate;

    public $month;

    public function mount()
    {
        $this->startDate = now()->toDateString();
        $this->endDate = now()->toDateString();
        $this->month = now()->format('Y-m');
    }

    #[Computed]
    public function reports()
    {
        if ($this->mode === 'daily') {

            return Attendance::with([
                'user.employeeAssignment.department',
                'user.employeeAssignment.position',
            ])
                ->when($this->startDate, fn ($q) => $q->whereDate('date', '>=', $this->startDate))
                ->when($this->endDate, fn ($q) => $q->whereDate('date', '<=', $this->endDate))
                ->latest('date')
                ->paginate(10);
        }

        // MONTHLY
        $start = Carbon::parse($this->month)->startOfMonth();
        $end = Carbon::parse($this->month)->endOfMonth();

        return Attendance::with('user.employeeAssignment.department')
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
