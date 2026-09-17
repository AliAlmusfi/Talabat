<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    /** @use HasFactory<\Database\Factories\LocationFactory> */
    use HasFactory;
    protected $fillable = [
        'name',
        'longitude',
        'latitude',
        'market_id'
    ];
    public function market()
    {
        return $this->belongsTo(Market::class);
    }
}
