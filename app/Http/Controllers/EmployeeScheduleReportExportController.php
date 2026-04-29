<?php

// app/Http/Controllers/EmployeeScheduleReportExportController.php

namespace App\Http\Controllers;

use App\Exports\EmployeeScheduleReportExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeScheduleReportExportController
{
    public function __invoke(Request $request)
    {
        $filters = [
            'month' => $request->month,
            'departmentId' => $request->departmentId,
            'userId' => $request->userId,
        ];

        return Excel::download(
            new EmployeeScheduleReportExport($filters),
            'employee-schedule-report.xlsx'
        );
    }
}
