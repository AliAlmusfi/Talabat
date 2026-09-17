<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    /** @use HasFactory<\Database\Factories\ReportFactory> */
    use HasFactory;

    protected $fillable = [
        'info',
        'info_ar',
        'type',
        'type_ar',
        'user_id',
        'market_id',
    ];

    public function user(){
        return $this->belongsTo(User::class , 'user_id');
    }
    public function market(){
        return $this->belongsTo(Market::class , 'market_id');
    }
}
