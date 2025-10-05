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

function generateOtpCode($length = 6): int
{
    $min = pow(10, $length - 1);
    $max = pow(10, $length) - 1;

    return random_int($min, $max);
}

function formatDate($date, string $format = 'd-M-Y H:i:s'): string
{
    return \Illuminate\Support\Carbon::parse($date)->format($format);
}

function getBlogImage($imagePath)
{

    $imageUrl = 'https://images.unsplash.com/photo-1555949963-aa79dcee981c?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&h=200';

    if ($imagePath && file_exists(storage_path('app/public/' . $imagePath))) {
        $imageUrl = asset('storage/' . $imagePath);
    }

    return $imageUrl;
}

function recentPosts($limit = 5)
{
    return \App\Models\Blog::latest()->limit($limit)->get();
}

function getValue($item, $key, $default = null) {
    if (is_array($item) && isset($item[$key])) return $item[$key];
    if (is_object($item) && isset($item->$key)) return $item->$key;
    return $default;
}

function getFormatted($item, $key) {
    if (is_array($item) && isset($item['_formatted'][$key])) return $item['_formatted'][$key];
    return getValue($item, $key);
}

function getCountryName($item) {
    if (is_array($item) && isset($item['country'])){

        if (is_int($item['country'])) {
            return \App\Models\Country::find($item['country'])->name;
        }
        return $item['country'];

    }
    // Eloquent model
    if (is_object($item) && isset($item->country->name)){
        return $item->country->name;
    }
    return '';
}

function generate_sku(?string $title = null): string
{
    // Base from title (letters & numbers only), up to 3 chars
    $base = $title
        ? preg_replace('/[^A-Z0-9]/', '', Str::upper(Str::slug($title, '')))
        : 'PRD';
    $base = $base ?: 'PRD';
    $base = Str::limit($base, 3, ''); // max 3 chars

    // Remaining length for random part
    $remaining = 8 - strlen($base); // total SKU = 8 chars

    for ($i = 0; $i < 20; $i++) {
        // Generate random alphanumeric string for remaining chars
        $rand = strtoupper(substr(Str::random($remaining), 0, $remaining));
        $candidate = $base . $rand;

        if (! \App\Models\Product::where('sku', $candidate)->exists()) {
            return $candidate;
        }
    }

    // Fallback: just random 8-char SKU
    return strtoupper(Str::random(8));
}
