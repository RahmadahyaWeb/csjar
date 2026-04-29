<?php

// app/Exports/LeaveBalanceSheet.php

namespace App\Exports;

use App\Models\Leave;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LeaveBalanceSheet implements FromCollection, WithHeadings
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function headings(): array
    {
        return ['Employee', 'Department', 'Quota', 'Used', 'Remaining'];
    }

    public function collection()
    {
        $quota = 12;

        return User::with('employeeAssignment.department')
            ->when($this->filters['departmentId'], function ($q) {
                $q->whereHas('employeeAssignment', function ($sub) {
                    $sub->where('department_id', $this->filters['departmentId']);
                });
            })
            ->when($this->filters['userId'], fn ($q) => $q->where('id', $this->filters['userId']))
            ->get()
            ->map(function ($user) use ($quota) {

                $used = Leave::where('user_id', $user->id)
                    ->where('status', 'approved')
                    ->whereYear('start_date', $this->filters['year'])
                    ->sum(DB::raw('DATEDIFF(end_date, start_date) + 1'));

                return [
                    $user->name,
                    optional($user->employeeAssignment?->department)->name,
                    $quota,
                    $used,
                    max(0, $quota - $used),
                ];
            });
    }
}
