<?php

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

new #[Title('Attendance Monitoring')] class extends Component
{
    use WithoutUrlPagination, WithPagination;

    public $startDate;

    public $endDate;

    public $status;

    public $userId;

    public function mount()
    {
        $this->startDate = now()->toDateString();
        $this->endDate = now()->toDateString();
    }

    #[Computed]
    public function users()
    {
        return User::orderBy('name')->get();
    }

    #[Computed]
    public function attendances()
    {
        return Attendance::with([
            'user.employeeAssignment.department',
            'user.employeeAssignment.position',
            'user.employeeSchedule.workSchedule.days.shift',
        ])
            ->when($this->startDate, fn ($q) => $q->whereDate('date', '>=', $this->startDate))
            ->when($this->endDate, fn ($q) => $q->whereDate('date', '<=', $this->endDate))
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->when($this->userId, fn ($q) => $q->where('user_id', $this->userId))
            ->latest('date')
            ->paginate(10)
            ->through(function ($item) {

                $dayOfWeek = Carbon::parse($item->date)->dayOfWeekIso;

                $shift = $item->user->employeeSchedule?->workSchedule?->days
                    ->firstWhere('day_of_week', $dayOfWeek)?->shift;

                $desc = [];

                if ($item->late_minutes > 0) {
                    if ($shift && $item->late_minutes <= ($shift->tolerance_late ?? 0)) {
                        $desc[] = 'Late (within tolerance)';
                    } else {
                        $desc[] = 'Late';
                    }
                }

                if ($item->early_leave_minutes > 0) {
                    if ($shift && $item->early_leave_minutes <= ($shift->tolerance_early_leave ?? 0)) {
                        $desc[] = 'Early Leave (within tolerance)';
                    } else {
                        $desc[] = 'Early Leave';
                    }
                }

                if ($item->overtime_minutes > 0) {
                    $desc[] = 'Overtime';
                }

                if ($item->status === 'absent') {
                    $desc[] = 'Absent';
                }

                if ($item->status === 'leave') {
                    $desc[] = 'On Leave';
                }

                if ($item->status === 'holiday') {
                    $desc[] = 'Holiday';
                }

                $item->description = implode(', ', $desc);

                return $item;
            });
    }

    #[Computed]
    public function summary()
    {
        $query = Attendance::query()
            ->when($this->startDate, fn ($q) => $q->whereDate('date', '>=', $this->startDate))
            ->when($this->endDate, fn ($q) => $q->whereDate('date', '<=', $this->endDate))
            ->when($this->userId, fn ($q) => $q->where('user_id', $this->userId));

        return [
            'present' => (clone $query)->where('status', 'present')->count(),
            'absent' => (clone $query)->where('status', 'absent')->count(),
            'leave' => (clone $query)->where('status', 'leave')->count(),
            'holiday' => (clone $query)->where('status', 'holiday')->count(),
        ];
    }
};
