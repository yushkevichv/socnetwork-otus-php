<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $fillable = [
        'created_at'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'chats_users')->withTimestamps();
    }
}
