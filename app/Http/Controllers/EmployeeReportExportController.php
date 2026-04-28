<?php

// app/Http/Controllers/EmployeeReportExportController.php

namespace App\Http\Controllers;

use App\Exports\EmployeeReportExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeReportExportController
{
    public function __invoke(Request $request)
    {
        $filters = [
            'departmentId' => $request->departmentId,
            'search' => $request->search,
        ];

        return Excel::download(
            new EmployeeReportExport($filters),
            'employee-report.xlsx'
        );
    }
}
