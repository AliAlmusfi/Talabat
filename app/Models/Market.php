<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Market extends Model
{
    /** @use HasFactory<\Database\Factories\MarketFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'image_url',
        'admin_id',
    ];

    public function admin(){
        return $this->belongsTo(Admin::class , 'admin_id');
    }
    public function locations(){
        return $this->hasMany(Location::class);
    }
    public function tags(){
        return $this->belongsToMany(Market_tag::class , 'market_tag', 'market_id', 'tag_id');
    }
}
