<?php

// app/Livewire/Pages/OvertimeApproval/Index.php

use App\Models\Attendance;
use App\Models\Department;
use App\Models\User;
use App\Services\PayrollService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Overtime Approval')] class extends Component
{
    use WithPagination;

    public $startDate;

    public $endDate;

    public $departmentId;

    public $userId;

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
    public function users()
    {
        return User::with('employeeAssignment')
            ->when($this->departmentId, function ($q) {
                $q->whereHas('employeeAssignment', function ($q2) {
                    $q2->where('department_id', $this->departmentId);
                });
            })
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function attendances()
    {
        return Attendance::with([
            'user.employeeAssignment.department',
            'user.employeeAssignment.position',
        ])
            ->whereBetween('date', [$this->startDate, $this->endDate])
            ->where('overtime_status', 'pending')
            ->where('overtime_minutes', '>', 0)
            ->when($this->departmentId, function ($q) {
                $q->whereHas('user.employeeAssignment', function ($q2) {
                    $q2->where('department_id', $this->departmentId);
                });
            })
            ->when($this->userId, fn ($q) => $q->where('user_id', $this->userId))
            ->latest('date')
            ->paginate(10);
    }

    public function approve($id)
    {
        $attendance = Attendance::findOrFail($id);

        $attendance->update([
            'overtime_status' => 'approved',
            'overtime_approved_by' => Auth::id(),
            'overtime_approved_at' => now(),
        ]);

        app(PayrollService::class)->generatePeriod(
            $attendance->user_id,
            $this->startDate,
            $this->endDate
        );
    }

    public function reject($id)
    {
        $attendance = Attendance::findOrFail($id);

        $attendance->update([
            'overtime_status' => 'rejected',
            'overtime_approved_by' => Auth::id(),
            'overtime_approved_at' => now(),
        ]);

        app(PayrollService::class)->generatePeriod(
            $attendance->user_id,
            $this->startDate,
            $this->endDate
        );
    }
};
