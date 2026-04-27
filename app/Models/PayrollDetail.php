<?php

// app/Models/PayrollDetail.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollDetail extends Model
{
    protected $fillable = [
        'payroll_id',
        'component_name',
        'amount',
        'type',
    ];

    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }
}
