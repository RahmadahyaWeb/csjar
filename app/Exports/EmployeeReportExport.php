<?php

// app/Exports/EmployeeReportExport.php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EmployeeReportExport implements FromCollection, WithHeadings
{
    protected $departmentId;

    protected $search;

    public function __construct($filters)
    {
        $this->departmentId = $filters['departmentId'];
        $this->search = $filters['search'];
    }

    public function headings(): array
    {
        return [
            'Name',
            'Email',
            'Branch',
            'Department',
            'Position',
            'Team',
        ];
    }

    public function collection()
    {
        return User::with('employeeAssignment.branch',
            'employeeAssignment.department',
            'employeeAssignment.position',
            'employeeAssignment.team')
            ->when($this->departmentId, function ($q) {
                $q->whereHas('employeeAssignment', function ($sub) {
                    $sub->where('department_id', $this->departmentId);
                });
            })
            ->when($this->search, function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%");
            })
            ->get()
            ->map(function ($emp) {
                return [
                    $emp->name,
                    $emp->email,
                    optional($emp->employeeAssignment?->branch)->name,
                    optional($emp->employeeAssignment?->department)->name,
                    optional($emp->employeeAssignment?->position)->name,
                    optional($emp->employeeAssignment?->team)->name,
                ];
            });
    }
}
