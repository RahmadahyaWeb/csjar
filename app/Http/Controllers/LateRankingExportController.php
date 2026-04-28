<?php

// app/Http/Controllers/LateRankingExportController.php

namespace App\Http\Controllers;

use App\Exports\LateRankingExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LateRankingExportController
{
    public function __invoke(Request $request)
    {
        $filters = [
            'startDate' => $request->startDate,
            'endDate' => $request->endDate,
            'departmentId' => $request->departmentId,
        ];

        return Excel::download(
            new LateRankingExport($filters),
            'late-ranking.xlsx'
        );
    }
}
