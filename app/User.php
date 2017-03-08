<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Friend;
use App\Block;

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

    /*public static function isOwner(User $loggedIn, User $owner)
    {
      //check to see if these two are the same guys then return true or false
      if(static->id == $owner->id)
      {
        return true;
      }
      else
      {
        return false;
      }
    }*/

    public function isFriend(User $owner)//checks if user is friends with user2 or vis versa
    {
      $friend = Friend::where('user1','=',$this->name)->where('user2','=',$owner)->where('accepted','=','1')->orWhere('user1','=',$owner)->where('user2','=',$this->name)->where('accepted','=','1')->get();
      //App/Friend::where('user1','=','Alvin')->where('user2','=','Palmer')->where('accepted','=','1')->orWhere('user1','=','Palmer')->where('user2','=','Alvin')->where('accepted','=','1')->get();
      if($friend > 0)
      {
        return true;
      }
      return false;

    }

    public function isBlocked(User $owner)//checks if user is friends with user2 or vis versa
    {
      /*
      $friend = Friend::where('user1','=',$this->name)->where('user2','=',$owner)->where('accepted','=','1')->orWhere('user1','=',$owner)->where('user2','=',$this->name)->where('accepted','=','1')->get();
      //App/Friend::where('user1','=','Alvin')->where('user2','=','Palmer')->where('accepted','=','1')->orWhere('user1','=','Palmer')->where('user2','=','Alvin')->where('accepted','=','1')->get();
      if($friend > 0)
      {
        return true;
      }
      return false;
      */
    }

}
