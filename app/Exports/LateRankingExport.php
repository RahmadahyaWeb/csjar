<?php

// app/Exports/LateRankingExport.php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LateRankingExport implements FromCollection, WithHeadings
{
    protected $startDate;

    protected $endDate;

    protected $departmentId;

    public function __construct($filters)
    {
        $this->startDate = $filters['startDate'];
        $this->endDate = $filters['endDate'];
        $this->departmentId = $filters['departmentId'];
    }

    public function headings(): array
    {
        return [
            'Rank',
            'Employee',
            'Department',
            'Late Days',
            'Total Late Minutes',
        ];
    }

    public function collection()
    {
        $data = Attendance::with('user.employeeAssignment.department')
            ->whereBetween('date', [$this->startDate, $this->endDate])
            ->where('status', 'present')
            ->when($this->departmentId, function ($q) {
                $q->whereHas('user.employeeAssignment', function ($sub) {
                    $sub->where('department_id', $this->departmentId);
                });
            })
            ->get()
            ->groupBy('user_id')
            ->map(function ($rows) {

                return [
                    'user' => $rows->first()->user,
                    'department' => optional($rows->first()->user->employeeAssignment?->department)->name,
                    'late_days' => $rows->where('late_minutes', '>', 0)->count(),
                    'total_late' => $rows->sum('late_minutes'),
                ];
            })
            ->sortByDesc('total_late')
            ->values();

        return collect($data)->map(function ($item, $index) {
            return [
                $index + 1,
                $item['user']->name,
                $item['department'] ?? '-',
                $item['late_days'],
                $item['total_late'],
            ];
        });
    }
}
