<?php

// app/Exports/OvertimeReportExport.php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class OvertimeReportExport implements WithMultipleSheets
{
    protected $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function sheets(): array
    {
        return [
            new OvertimeSummarySheet($this->filters),
            new OvertimeDetailSheet($this->filters),
        ];
    }
}
