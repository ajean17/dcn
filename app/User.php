<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Friend;
use App\Block;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = ['name', 'first','last','city','state','email','password','activated',];

    protected $hidden = ['password', 'remember_token',];

    public function getroutekeyname()//returns the name of the user as it matches in the route
    {
      return 'name';
    }
    public function profile()
    {
      return $this->hasOne(Profile::class);
    }
    public function messages()//Pulls the messages sent by a User
    {
      return $this->hasMany(Message::class);
    }
    public function friends()//Pulls the friends of a User
    {
      return $this->hasMany(Friend::class);
    }

    public function blocked()//Pulls the friends of a User
    {
      return $this->hasMany(Block::class);
    }
}
