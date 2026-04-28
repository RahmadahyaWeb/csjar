<?php

// app/Http/Controllers/OvertimeReportExportController.php

namespace App\Http\Controllers;

use App\Exports\OvertimeReportExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class OvertimeReportExportController
{
    public function __invoke(Request $request)
    {
        $filters = [
            'startDate' => $request->startDate,
            'endDate' => $request->endDate,
            'departmentId' => $request->departmentId,
        ];

        return Excel::download(
            new OvertimeReportExport($filters),
            'overtime-report.xlsx'
        );
    }
}
