<?php

// app/Exports/EmployeeScheduleReportExport.php

namespace App\Exports;

use App\Models\EmployeeSchedule;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EmployeeScheduleReportExport implements FromCollection, WithHeadings
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
            'Schedule',
            'Start Date',
            'End Date',
        ];
    }

    public function collection()
    {
        $start = Carbon::parse($this->filters['month'])->startOfMonth();
        $end = Carbon::parse($this->filters['month'])->endOfMonth();

        return EmployeeSchedule::with('user.employeeAssignment.department', 'workSchedule')
            ->when($this->filters['departmentId'], function ($q) {
                $q->whereHas('user.employeeAssignment', function ($sub) {
                    $sub->where('department_id', $this->filters['departmentId']);
                });
            })
            ->when($this->filters['userId'], fn ($q) => $q->where('user_id', $this->filters['userId']))
            ->whereDate('start_date', '<=', $end)
            ->where(function ($q) use ($start) {
                $q->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', $start);
            })
            ->get()
            ->map(function ($item) {
                return [
                    $item->user->name,
                    optional($item->user->employeeAssignment?->department)->name,
                    $item->workSchedule->name,
                    $item->start_date,
                    $item->end_date ?? '-',
                ];
            });
    }
}
