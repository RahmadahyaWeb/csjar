<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    protected $fillable = [
        'department_id',
        'name',
        'code',
        'level',
        'parent_id',
        'head_user_id',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function parent()
    {
        return $this->belongsTo(Position::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Position::class, 'parent_id');
    }

    public function head()
    {
        return $this->belongsTo(User::class, 'head_user_id');
    }
}
