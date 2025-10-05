<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaleItem extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'sale_id',
        'product_id',
        'quantity',
        'price',
        'discount',
        'total',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class,'product_id','id');
    }
}
