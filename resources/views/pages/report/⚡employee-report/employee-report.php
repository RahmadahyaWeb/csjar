<?php

// app/Livewire/Pages/EmployeeReport/Index.php

use App\Models\Department;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Employee Report')] class extends Component
{
    public $departmentId;

    public $search;

    #[Computed]
    public function departments()
    {
        return Department::orderBy('name')->get();
    }

    #[Computed]
    public function employees()
    {
        return User::with([
            'employeeAssignment.branch',
            'employeeAssignment.department',
            'employeeAssignment.position',
            'employeeAssignment.team',
        ])
            ->when($this->departmentId, function ($q) {
                $q->whereHas('employeeAssignment', function ($sub) {
                    $sub->where('department_id', $this->departmentId);
                });
            })
            ->when($this->search, function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%");
            })
            ->orderBy('name')
            ->get();
    }
};
