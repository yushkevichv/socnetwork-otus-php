<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'last_name',
        'birthday',
        'gender',
        'city',
        'interests',
        'gender',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getGenderAttribute($value)
    {
        switch ($value) {
            case 0:
                return __('user.gender_undefined');
            break;
            case 1:
                return __('user.gender_male');
            break;
            case 2:
                return __('user.gender_female');
            break;
            default:
                return __('user.gender_undefined');
            break;
        }

    }
}
