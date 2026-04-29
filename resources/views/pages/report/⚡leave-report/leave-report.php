<?php

// app/Livewire/Pages/LeaveReport/Index.php

use App\Models\Department;
use App\Models\Leave;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Leave Report')] class extends Component
{
    public $departmentId;

    public $userId;

    public $year;

    public function mount()
    {
        $this->year = now()->year;
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
        })->orderBy('name')->get();
    }

    #[Computed]
    public function summary()
    {
        $query = Leave::query()
            ->whereYear('start_date', $this->year);

        if ($this->departmentId) {
            $query->whereHas('user.employeeAssignment', function ($q) {
                $q->where('department_id', $this->departmentId);
            });
        }

        return [
            'pending' => (clone $query)->where('status', 'pending')->count(),
            'approved' => (clone $query)->where('status', 'approved')->count(),
            'rejected' => (clone $query)->where('status', 'rejected')->count(),
        ];
    }

    #[Computed]
    public function balances()
    {
        $annualQuota = 12;

        return User::with('employeeAssignment.department')
            ->when($this->departmentId, function ($q) {
                $q->whereHas('employeeAssignment', function ($sub) {
                    $sub->where('department_id', $this->departmentId);
                });
            })
            ->when($this->userId, fn ($q) => $q->where('id', $this->userId))
            ->get()
            ->map(function ($user) use ($annualQuota) {

                $used = Leave::where('user_id', $user->id)
                    ->where('status', 'approved')
                    ->whereYear('start_date', $this->year)
                    ->sum(DB::raw('DATEDIFF(end_date, start_date) + 1'));

                return [
                    'user' => $user,
                    'department' => optional($user->employeeAssignment?->department)->name,
                    'quota' => $annualQuota,
                    'used' => $used,
                    'remaining' => max(0, $annualQuota - $used),
                ];
            });
    }

    #[Computed]
    public function approvals()
    {
        return Leave::with('user.employeeAssignment.department')
            ->whereYear('start_date', $this->year)
            ->when($this->departmentId, function ($q) {
                $q->whereHas('user.employeeAssignment', function ($sub) {
                    $sub->where('department_id', $this->departmentId);
                });
            })
            ->when($this->userId, fn ($q) => $q->where('user_id', $this->userId))
            ->latest('start_date')
            ->get();
    }
};
