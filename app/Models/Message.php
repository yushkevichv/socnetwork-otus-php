<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'chat_id',
        'user_id',
        'author_id',
        'text',
        'created_at',
    ];


    public function user()
    {
        $this->connection = 'mysql';
        return $this->belongsTo(User::class);
    }

    public function author()
    {
        $this->connection = 'mysql';
        return $this->belongsTo(User::class, 'author_id');
    }
}
