<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
  protected $fillable = ['username','message'];

  public function user()//to find a user's profile
  {
    return $this->belongsTo(User::class);
  }
}
