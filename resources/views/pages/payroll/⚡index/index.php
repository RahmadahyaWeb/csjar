<?php

// app/Livewire/Pages/Payroll/Index.php

use App\Models\Department;
use App\Models\Payroll;
use App\Models\User;
use App\Services\PayrollService;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Payroll')] class extends Component
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
    public function payrolls()
    {
        return Payroll::with([
            'user.employeeAssignment.department',
        ])
            ->whereDate('start_date', '>=', $this->startDate)
            ->whereDate('end_date', '<=', $this->endDate)
            ->when($this->departmentId, function ($q) {
                $q->whereHas('user.employeeAssignment', function ($q2) {
                    $q2->where('department_id', $this->departmentId);
                });
            })
            ->when($this->userId, fn ($q) => $q->where('user_id', $this->userId))
            ->latest('start_date')
            ->paginate(10);
    }

    public function generate()
    {
        try {

            $users = User::query()
                ->whereDoesntHave('roles', function ($q) {
                    $q->where('name', 'super_admin');
                })
                ->when($this->departmentId, function ($q) {
                    $q->whereHas('employeeAssignment', function ($q2) {
                        $q2->where('department_id', $this->departmentId);
                    });
                })
                ->when($this->userId, fn ($q) => $q->where('id', $this->userId))
                ->pluck('id');

            foreach ($users as $userId) {

                app(PayrollService::class)
                    ->generatePeriod(
                        $userId,
                        $this->startDate,
                        $this->endDate
                    );
            }

            Flux::toast(
                heading: 'Success',
                text: 'Payroll generated',
                variant: 'success'
            );

        } catch (Throwable $e) {

            Flux::toast(
                heading: 'Error',
                text: $e->getMessage(),
                variant: 'danger'
            );
        }
    }
};
