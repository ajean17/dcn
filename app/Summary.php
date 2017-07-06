<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Summary extends Model
{
  protected $fillable = [
      'user_id', 'product_name', 'market', 'age_range', 'region', 'market_other', 'competitor1',
      'competitor3','competitor2','liquidity','ROI',
  ];

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function profile()
  {
    return $this->belongsTo(Profile::class);
  }
}
