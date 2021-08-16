<?php

namespace Database\Seeders;

use Domain\Entities\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => Str::random(10),
            'email' => Str::random(10).'@gmail.com',
            'password' => Hash::make('password'),
            'register_number' => '25350390000150',
            'type' => User::SHOPKEEPER,
            'funds' => '1000'
        ]);

        DB::table('users')->insert([
            'name' => Str::random(10),
            'email' => Str::random(10).'@gmail.com',
            'password' => Hash::make('password'),
            'register_number' => '91263413013',
            'type' => User::CUSTOMER,
            'funds' => '500'
        ]);

        DB::table('users')->insert([
            'name' => Str::random(10),
            'email' => Str::random(10).'@gmail.com',
            'password' => Hash::make('password'),
            'register_number' => '11196955034',
            'type' => User::CUSTOMER,
            'funds' => '500'
        ]);
    }
}
