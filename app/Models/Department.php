<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = [
        'branch_id',
        'parent_id',
        'name',
        'code',
        'head_user_id',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function parent()
    {
        return $this->belongsTo(Department::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Department::class, 'parent_id');
    }

    public function head()
    {
        return $this->belongsTo(User::class, 'head_user_id');
    }

    public function positions()
    {
        return $this->hasMany(Position::class);
    }

    public function teams()
    {
        return $this->hasMany(Team::class);
    }
}
