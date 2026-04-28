<?php

// app/Exports/OvertimeSummarySheet.php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OvertimeSummarySheet implements FromCollection, WithHeadings
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function headings(): array
    {
        return [
            'Rank',
            'Employee',
            'Department',
            'Overtime Days',
            'Total Overtime Minutes',
        ];
    }

    public function collection()
    {
        $data = Attendance::with('user.employeeAssignment.department')
            ->whereBetween('date', [$this->filters['startDate'], $this->filters['endDate']])
            ->where('status', 'present')
            ->when($this->filters['departmentId'], function ($q) {
                $q->whereHas('user.employeeAssignment', function ($sub) {
                    $sub->where('department_id', $this->filters['departmentId']);
                });
            })
            ->get()
            ->groupBy('user_id')
            ->map(function ($rows) {

                return [
                    'user' => $rows->first()->user,
                    'department' => optional($rows->first()->user->employeeAssignment?->department)->name,
                    'overtime_days' => $rows->where('overtime_minutes', '>', 0)->count(),
                    'total_overtime' => $rows->sum('overtime_minutes'),
                ];
            })
            ->filter(fn ($item) => $item['total_overtime'] > 0)
            ->sortByDesc('total_overtime')
            ->values();

        return collect($data)->map(function ($item, $index) {
            return [
                $index + 1,
                $item['user']->name,
                $item['department'] ?? '-',
                $item['overtime_days'],
                $item['total_overtime'],
            ];
        });
    }
}
