<?php

// app/Http/Controllers/AttendanceReportExportController.php

namespace App\Http\Controllers;

use App\Exports\AttendanceReportExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceReportExportController
{
    public function __invoke(Request $request)
    {
        $filters = [
            'mode' => $request->mode,
            'startDate' => $request->startDate,
            'endDate' => $request->endDate,
            'month' => $request->month,
            'departmentId' => $request->departmentId,
            'userId' => $request->userId,
        ];

        return Excel::download(
            new AttendanceReportExport($filters),
            'attendance-report.xlsx'
        );
    }
}
