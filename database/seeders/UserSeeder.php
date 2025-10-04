<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        for ($i = 1; $i <= 50; $i++) {

            $data = [
                'name' => $faker->name,
                'role' => USER_ROLE,
                'email' => $faker->unique()->safeEmail,
                'password' => Hash::make('12345678'),
                'address' => $faker->address,
            ];


            User::firstOrCreate(
                ['email' => $data['email']],
                $data
            );
        }
    }
}
