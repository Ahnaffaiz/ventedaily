<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        foreach (range(1, 10) as $index) {
            DB::table('suppliers')->insert([
                'name'    => $faker->company,
                'phone'   => '8' . $faker->randomNumber(8, true),
                'address' => $faker->address,
            ]);
        }
    }
}
