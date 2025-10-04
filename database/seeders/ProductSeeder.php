<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \Faker\Factory::create()->seed(123);
        $faker = \Faker\Factory::create();

        for ($i = 0; $i < 50; $i++) {
            Product::create([
                'name'        => $faker->words(3, true),
                'sku'         => strtoupper(Str::random(8)),
                'price'       => $faker->randomFloat(2, 10, 500),
                'quantity'    => $faker->numberBetween(1, 100),
                'description' => $faker->sentence(10),
                'status'      => $faker->randomElement([ACTIVE_STATUS, INACTIVE_STATUS]),
                'slug'        => Str::slug($faker->words(3, true)) . '-' . time(),
            ]);
        }
    }
}
