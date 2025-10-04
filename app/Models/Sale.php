<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'user_id',
        'sale_date',
        'subtotal',
        'discount_total',
        'total_amount',
        'status',
    ];
}
