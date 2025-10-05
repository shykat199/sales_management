<?php

function active_class($path, $active = 'active') {
    return request()->is(...(array)$path) ? $active : '';
}

function is_active_route($path) {
    return request()->is(...(array)$path) ? 'true' : 'false';
}

function show_class($path) {
    return request()->is(...(array)$path) ? 'show' : '';
}


function getValue($item, $key, $default = null) {
    if (is_array($item) && isset($item[$key])) return $item[$key];
    if (is_object($item) && isset($item->$key)) return $item->$key;
    return $default;
}


function generate_sku(?string $title = null): string
{
    $base = $title
        ? preg_replace('/[^A-Z0-9]/', '', Str::upper(Str::slug($title, '')))
        : 'PRD';
    $base = $base ?: 'PRD';
    $base = Str::limit($base, 3, '');

    $remaining = 8 - strlen($base);

    for ($i = 0; $i < 20; $i++) {
        $rand = strtoupper(substr(Str::random($remaining), 0, $remaining));
        $candidate = $base . $rand;

        if (! \App\Models\Product::where('sku', $candidate)->exists()) {
            return $candidate;
        }
    }

    return strtoupper(Str::random(8));
}


function calculateSaleTotal(array $items): array
{
    $subtotal = collect($items)->sum('price');
    $discount_total = collect($items)->sum(function ($item) {
        return ($item['price'] * $item['qty']) * ($item['discount'] ?? 0) / 100;
    });
    $total_amount = $subtotal - $discount_total;

    return [
        'subtotal' => $subtotal,
        'discount_total' => $discount_total,
        'total_amount' => $total_amount,
    ];
}
