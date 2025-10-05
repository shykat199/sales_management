<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name',
        'sku',
        'price',
        'quantity',
        'description',
        'status',
        'slug',
        'image'
    ];

    public function getPriceAttribute()
    {
        return number_format($this->attributes['price'], 2, '.', ',');
    }

    public function getNameAttribute()
    {
        return ucwords($this->attributes['name']);
    }
}
