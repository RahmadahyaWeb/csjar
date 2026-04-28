<?php

// app/Exports/PayrollSummarySheet.php

namespace App\Exports;

use App\Models\Payroll;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PayrollSummarySheet implements FromCollection, WithHeadings
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function headings(): array
    {
        return [
            'Department',
            'Total Employee',
            'Total Earning',
            'Total Deduction',
            'Total Net',
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
            ->groupBy(fn ($item) => optional($item->user->employeeAssignment?->department)->name)
            ->map(function ($rows, $dept) {
                return [
                    $dept ?? '-',
                    $rows->count(),
                    $rows->sum('total_earning'),
                    $rows->sum('total_deduction'),
                    $rows->sum('net_salary'),
                ];
            })
            ->values();
    }
}
