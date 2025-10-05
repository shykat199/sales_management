<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'user_id',
        'customer_id',
        'sale_date',
        'subtotal',
        'discount_total',
        'total_amount',
        'status',
    ];

    public function getTotalPriceAttribute()
    {
        return number_format($this->attributes['total_amount'], 2, '.', ',').' BDT';
    }

    public function note()
    {
        return $this->morphMany(Note::class, 'noteable');
    }
}
