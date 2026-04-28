<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserFace extends Model
{
    protected $fillable = [
        'user_id',
        'descriptor',
    ];

    protected $casts = [
        'descriptor' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
