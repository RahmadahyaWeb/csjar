<?php

// app/Http/Controllers/PayrollReportExportController.php

namespace App\Http\Controllers;

use App\Exports\PayrollReportExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PayrollReportExportController
{
    public function __invoke(Request $request)
    {
        $filters = [
            'startDate' => $request->startDate,
            'endDate' => $request->endDate,
            'departmentId' => $request->departmentId,
        ];

        return Excel::download(
            new PayrollReportExport($filters),
            'payroll-report.xlsx'
        );
    }
}
