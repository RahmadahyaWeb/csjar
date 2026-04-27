<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = [
        'name',
        'code',
        'latitude',
        'longitude',
        'radius',
    ];

    public function departments()
    {
        return $this->hasMany(Department::class);
    }

    public function assignments()
    {
        return $this->hasMany(EmployeeAssignment::class);
    }
}
