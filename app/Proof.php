<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Proof extends Model
{
  protected $fillable = [
      'user_id', 'embed', 'title',
  ];

  public function user()
  {
    return $this->belongsTo(User::class);
  }
}
