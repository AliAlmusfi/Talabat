<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product_tag extends Model
{
    /** @use HasFactory<\Database\Factories\ProductTagFactory> */
    use HasFactory;

    protected $table = 'product_tag_listing';

    protected $fillable = [
        'tag',
    ];

    public function products(){
        return $this->belongsToMany(Product::class , 'product_tag' , 'tag_id' , 'product_id');
    }
}
