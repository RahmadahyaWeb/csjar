<?php

// app/Livewire/Pages/Payroll/Report.php

use App\Models\Department;
use App\Models\Payroll;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Payroll Report')] class extends Component
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
        $query = Payroll::query()
            ->whereBetween('start_date', [$this->startDate, $this->endDate]);

        return [
            'total_employee' => (clone $query)->count(),
            'total_earning' => (clone $query)->sum('total_earning'),
            'total_deduction' => (clone $query)->sum('total_deduction'),
            'total_net' => (clone $query)->sum('net_salary'),
        ];
    }

    #[Computed]
    public function byDepartment()
    {
        return Payroll::with('user.employeeAssignment.department')
            ->whereBetween('start_date', [$this->startDate, $this->endDate])
            ->get()
            ->groupBy(fn ($item) => optional($item->user->employeeAssignment?->department)->name)
            ->map(function ($rows) {
                return [
                    'total_employee' => $rows->count(),
                    'total_earning' => $rows->sum('total_earning'),
                    'total_deduction' => $rows->sum('total_deduction'),
                    'total_net' => $rows->sum('net_salary'),
                ];
            });
    }
};
