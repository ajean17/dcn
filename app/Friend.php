<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Friend extends Model
{

  protected $fillable = ['user1', 'user2', 'datemade'];

  public function user()//to find a user's profile
  {
    return $this->belongsTo(User::class);
  }
}
