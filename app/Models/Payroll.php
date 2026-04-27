<?php

// app/Models/Payroll.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    protected $fillable = [
        'user_id',
        'period',
        'total_earning',
        'total_deduction',
        'net_salary',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(PayrollDetail::class);
    }
}
