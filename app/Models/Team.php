<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = [
        'department_id',
        'name',
        'code',
        'lead_user_id',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function lead()
    {
        return $this->belongsTo(User::class, 'lead_user_id');
    }

    public function assignments()
    {
        return $this->hasMany(EmployeeAssignment::class);
    }
}
