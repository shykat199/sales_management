<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'user_id',
        'customer_id',
        'sale_date',
        'subtotal',
        'discount_total',
        'total_amount',
        'status',
    ];

    public function getSubtotalAttribute()
    {
        return number_format($this->attributes['subtotal'], 2, '.', ',').' BDT';

    }

    public function getFormattedTotalAmountAttribute()
    {
        return number_format($this->attributes['total_amount'], 2, '.', ',').' BDT';
    }

    public function getFormattedDiscountTotalAttribute()
    {
        return number_format($this->attributes['discount_total'], 2, '.', ',').' BDT';
    }


    public function note()
    {
        return $this->morphMany(Note::class, 'noteable');
    }

    public function customer(){
        return $this->belongsTo(User::class, 'customer_id','id');
    }
    public function createdBy(){
        return $this->belongsTo(User::class, 'user_id','id');
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class,'sale_id','id');
    }
}
