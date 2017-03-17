<?php
namespace App;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Model extends Eloquent
{
   //protected $fillable = ['title', 'body']; Allow these, black list everything else
  // $guarded block only this, allow everything else.
  protected $guarded = [];

}
