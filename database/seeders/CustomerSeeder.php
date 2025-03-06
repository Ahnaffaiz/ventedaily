<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        foreach (range(1, 10) as $index) {
            DB::table('customers')->insert([
                'name'    => $faker->name,
                'phone'   => '8' . $faker->randomNumber(8, true),
                'email'   => $faker->safeEmail,
                'group_id'=> $faker->numberBetween(1, 2),
                'address' => $faker->address,
            ]);
        }
    }
}
