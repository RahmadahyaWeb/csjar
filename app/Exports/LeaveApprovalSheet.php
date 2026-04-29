<?php

// app/Exports/LeaveApprovalSheet.php

namespace App\Exports;

use App\Models\Leave;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LeaveApprovalSheet implements FromCollection, WithHeadings
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function headings(): array
    {
        return ['Employee', 'Department', 'Start', 'End', 'Status'];
    }

    public function collection()
    {
        return Leave::with('user.employeeAssignment.department')
            ->whereYear('start_date', $this->filters['year'])
            ->when($this->filters['departmentId'], function ($q) {
                $q->whereHas('user.employeeAssignment', function ($sub) {
                    $sub->where('department_id', $this->filters['departmentId']);
                });
            })
            ->when($this->filters['userId'], fn ($q) => $q->where('user_id', $this->filters['userId']))
            ->get()
            ->map(fn ($l) => [
                $l->user->name,
                optional($l->user->employeeAssignment?->department)->name,
                $l->start_date,
                $l->end_date,
                $l->status,
            ]);
    }
}
