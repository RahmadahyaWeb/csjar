<?php

namespace App\Services;

use App\Models\Payroll;
use Barryvdh\DomPDF\Facade\Pdf;

class PayrollSlipService
{
    public function generate(Payroll $payroll)
    {
        $payroll->load('user', 'details');

        $html = view('pdf.payroll-slip', compact('payroll'))->render();

        return Pdf::loadHTML($html)
            ->setPaper('a4')
            ->download('payroll-'.$payroll->id.'.pdf');
    }
}
