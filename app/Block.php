<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Block extends Model
{
  /*
  If table variable isnt specified, eloquent will attempt to find the
      plural of the model in the database table list.
    [EX: blocks(for Block) instead of blocked.]
  */
  protected $table = 'blocked';

  public function user()//to find a user's profile
  {
    return $this->belongsTo(User::class);
  }
}
