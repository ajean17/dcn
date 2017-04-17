<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
  protected $fillable = [
      'user1', 'user2', 'message',
  ];
  protected $dates = ['created_at', 'updated_at', 'deleted_at'];
  public function user()//to find a user's profile
  {
    return $this->belongsTo(User::class);
  }
}
