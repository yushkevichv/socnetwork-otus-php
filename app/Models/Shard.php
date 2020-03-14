<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shard extends Model
{
    protected $fillable = [
        'name',
        'host',
        'port',
        'username',
        'db_name',
        'password'
    ];

}
