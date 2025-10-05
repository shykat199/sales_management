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


    protected static function booted()
    {
        // Set slug on create
        static::creating(function (Product $product) {
            if (empty($product->slug)) {
                $product->slug = static::generateUniqueSlug($product->name);
            }
        });

        // Update slug if title changed
        static::updating(function (Product $product) {
            if ($product->isDirty('name')) {
                $product->slug = static::generateUniqueSlug($product->name, $product->id);
            }
        });
    }

    /**
     * Create a unique slug from title.
     *
     * @param  string      $title
     * @param  int|null    $ignoreId  (when updating, ignore current record)
     * @param  string      $column
     * @return string
     */
    public static function generateUniqueSlug(string $title, int $ignoreId = null, string $column = 'slug'): string
    {
        $base = \Str::slug($title);
        if ($base === '') {
            $base = 'product';
        }

        $query = static::query()->where($column, $base)->orWhere($column, 'LIKE', $base . '-%');

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        $existing = $query->pluck($column);

        if (!$existing->contains($base)) {
            return $base;
        }

        $max = 0;
        $pattern = '/^'.preg_quote($base, '/').'-(\d+)$/';

        foreach ($existing as $slug) {
            if (preg_match($pattern, $slug, $m)) {
                $num = (int) $m[1];
                if ($num > $max) $max = $num;
            }
        }

        return $base . '-' . ($max + 1);
    }

    public function getPriceAttribute()
    {
        return number_format($this->attributes['price'], 2, '.', ',');
    }

    public function getNameAttribute()
    {
        return ucwords($this->attributes['name']);
    }
}
