<?php

// app/Exports/LeaveReportExport.php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class LeaveReportExport implements WithMultipleSheets
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function sheets(): array
    {
        return [
            new LeaveBalanceSheet($this->filters),
            new LeaveApprovalSheet($this->filters),
        ];
    }
}
