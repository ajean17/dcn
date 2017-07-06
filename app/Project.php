<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
  protected $fillable = [
      'profileId', 'name', 'elementOne', 'elementTwo', 'elementThree', 'elementFour', 'elementFive',
      'oneType','twoType','threeType','fourType','fiveType','category','subCategory',
      'oneName','twoName','threeName','fourName','fiveName',
  ];

  public function profile()
  {
    return $this->belongsTo(Profile::class);
  }
}
