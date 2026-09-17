<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'name_ar',
        'price',
        'quantity',
        'info',
        'info_ar',
        'image_url',
        'location_id'

    ];
    public function order(){
        return $this->belongsToMany(Order::class , 'order_product');
    }

    public function tag(){
        return $this->belongsToMany(Product_tag::class , 'product_tag' , 'product_id' , 'tag_id');
    }

    public function location(){
        return $this->belongsTo(Location::class , 'location_id' , 'id');
    }



}
