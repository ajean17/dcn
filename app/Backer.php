<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Backer extends Model
{
  protected $fillable = [
      'backing_id', 'backer_id', 'backer_name',
  ];

  public function user()
  {
    return $this->belongsTo(User::class);
  }
}
