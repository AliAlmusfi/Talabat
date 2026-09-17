<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Market_tag extends Model
{
    /** @use HasFactory<\Database\Factories\MarketTagFactory> */
    use HasFactory;

    protected $table = 'market_tag_listing';
    protected $fillable = ['tag'];
    public function markets(){
        return $this->belongsToMany(Market::class , 'market_tag', 'tag_id', 'market_id');
    }
}
