<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;


class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $data=[
            'name' => 'Admin User',
            'role' =>  ADMIN_ROLE,
            'email' => 'admin@gmail.com',
            'password' => Hash::make('12345678'),
            'address' => $faker->address,
        ];

        User::firstOrCreate(
            ['email' => $data['email']],
            $data
        );
    }
}
