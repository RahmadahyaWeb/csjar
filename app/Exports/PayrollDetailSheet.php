<?php

// app/Exports/PayrollDetailSheet.php

namespace App\Exports;

use App\Models\Payroll;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PayrollDetailSheet implements FromCollection, WithHeadings
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function headings(): array
    {
        return [
            'Employee',
            'Department',
            'Period Start',
            'Period End',
            'Earning',
            'Deduction',
            'Net Salary',
        ];
    }

    public function collection()
    {
        return Payroll::with('user.employeeAssignment.department')
            ->whereBetween('start_date', [$this->filters['startDate'], $this->filters['endDate']])
            ->when($this->filters['departmentId'], function ($q) {
                $q->whereHas('user.employeeAssignment', function ($sub) {
                    $sub->where('department_id', $this->filters['departmentId']);
                });
            })
            ->get()
            ->map(function ($item) {
                return [
                    $item->user->name,
                    optional($item->user->employeeAssignment?->department)->name,
                    $item->start_date,
                    $item->end_date,
                    $item->total_earning,
                    $item->total_deduction,
                    $item->net_salary,
                ];
            });
    }
}
