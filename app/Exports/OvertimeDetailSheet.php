<?php

// app/Exports/OvertimeDetailSheet.php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OvertimeDetailSheet implements FromCollection, WithHeadings
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
            'Date',
            'Overtime Minutes',
        ];
    }

    public function collection()
    {
        return Attendance::with('user.employeeAssignment.department')
            ->whereBetween('date', [$this->filters['startDate'], $this->filters['endDate']])
            ->where('status', 'present')
            ->where('overtime_minutes', '>', 0)
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
                    $item->date->format('Y-m-d'),
                    $item->overtime_minutes,
                ];
            });
    }
}
