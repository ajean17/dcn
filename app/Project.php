<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
  protected $fillable = [
      'profileId', 'name', 'elementOne', 'elementTwo', 'elementThree', 'elementFour', 'elementFive',
  ];
}
