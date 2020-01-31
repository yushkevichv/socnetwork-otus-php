<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'chat_id',
        'user_id',
        'is_author',
        'text',
        'created_at',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
