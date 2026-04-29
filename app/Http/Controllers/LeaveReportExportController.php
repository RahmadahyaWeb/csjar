<?php

// app/Http/Controllers/LeaveReportExportController.php

namespace App\Http\Controllers;

use App\Exports\LeaveReportExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LeaveReportExportController
{
    public function __invoke(Request $request)
    {
        return Excel::download(
            new LeaveReportExport($request->all()),
            'leave-report.xlsx'
        );
    }
}
