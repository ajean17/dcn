<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
  protected $fillable = [
      'username', 'hasVideo','hasMentor','hasInvestments','hasROI','projectOneID','projectTwoID',
  ];
}
