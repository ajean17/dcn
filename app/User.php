<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getroutekeyname()//returns the name of the user as it matches in the route
    {
      return 'name';
    }

    public function profile()//Pulls the user's profile
    {
      return $this->hasOne(Profile::class);
    }

    public function messages()//Pulls the messages sent by a User
    {
      return $this->hasMany(Message::class);
    }
}
