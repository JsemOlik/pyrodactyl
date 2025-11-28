<?php

namespace Pterodactyl\Models;

use Illuminate\Database\Eloquent\Model;

class UserServerOrder extends Model
{
    protected $fillable = ['user_id', 'order', 'sort_option'];

    protected $casts = [
        'order' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
