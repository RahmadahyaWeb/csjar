<?php

// app/Exports/PayrollReportExport.php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PayrollReportExport implements WithMultipleSheets
{
    protected $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function sheets(): array
    {
        return [
            new PayrollSummarySheet($this->filters),
            new PayrollDetailSheet($this->filters),
        ];
    }
}
