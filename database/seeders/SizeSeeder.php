<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sizes')->insert([
            ['name' => 'XS', 'desc' => 'Extra Small'],
            ['name' => 'S', 'desc' => 'Small'],
            ['name' => 'M', 'desc' => 'Medium'],
            ['name' => 'L', 'desc' => 'Large'],
            ['name' => 'XL', 'desc' => 'Extra Large'],
            ['name' => 'XXL', 'desc' => 'Double Extra Large'],
            ['name' => 'XXXL', 'desc' => 'Triple Extra Large'],
        ]);
    }
}
