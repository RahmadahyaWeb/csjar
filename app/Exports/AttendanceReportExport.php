<?php

// app/Exports/AttendanceReportExport.php

namespace App\Exports;

use App\Models\Attendance;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AttendanceReportExport implements FromCollection, WithHeadings
{
    protected $mode;

    protected $startDate;

    protected $endDate;

    protected $month;

    protected $departmentId;

    protected $userId;

    public function __construct($filters)
    {
        $this->mode = $filters['mode'];
        $this->startDate = $filters['startDate'];
        $this->endDate = $filters['endDate'];
        $this->month = $filters['month'];
        $this->departmentId = $filters['departmentId'];
        $this->userId = $filters['userId'];
    }

    public function headings(): array
    {
        if ($this->mode === 'daily') {
            return [
                'Name',
                'Department',
                'Date',
                'Status',
                'Work Minutes',
                'Late Minutes',
                'Overtime Minutes',
            ];
        }

        return [
            'Name',
            'Department',
            'Present',
            'Absent',
            'Leave',
            'Holiday',
            'Work Minutes',
            'Late Minutes',
            'Overtime Minutes',
        ];
    }

    public function collection()
    {
        $query = Attendance::with('user.employeeAssignment.department')
            ->when($this->departmentId, function ($q) {
                $q->whereHas('user.employeeAssignment', function ($sub) {
                    $sub->where('department_id', $this->departmentId);
                });
            })
            ->when($this->userId, fn ($q) => $q->where('user_id', $this->userId));

        if ($this->mode === 'daily') {

            return $query
                ->when($this->startDate, fn ($q) => $q->whereDate('date', '>=', $this->startDate))
                ->when($this->endDate, fn ($q) => $q->whereDate('date', '<=', $this->endDate))
                ->get()
                ->map(function ($item) {
                    return [
                        $item->user->name,
                        optional($item->user->employeeAssignment?->department)->name,
                        $item->date->format('Y-m-d'),
                        $item->status,
                        $item->work_minutes,
                        $item->late_minutes,
                        $item->overtime_minutes,
                    ];
                });
        }

        $start = Carbon::parse($this->month)->startOfMonth();
        $end = Carbon::parse($this->month)->endOfMonth();

        return $query
            ->whereBetween('date', [$start, $end])
            ->get()
            ->groupBy('user_id')
            ->map(function ($items) {
                return [
                    $items->first()->user->name,
                    optional($items->first()->user->employeeAssignment?->department)->name,
                    $items->where('status', 'present')->count(),
                    $items->where('status', 'absent')->count(),
                    $items->where('status', 'leave')->count(),
                    $items->where('status', 'holiday')->count(),
                    $items->sum('work_minutes'),
                    $items->sum('late_minutes'),
                    $items->sum('overtime_minutes'),
                ];
            })
            ->values();
    }
}
