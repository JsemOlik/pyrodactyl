<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserServerOrder extends Model
{
    protected $fillable = ['user_id', 'order'];

    protected $casts = [
        'order' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
