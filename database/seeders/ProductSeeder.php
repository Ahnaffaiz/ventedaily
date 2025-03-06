<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // Ambil semua ID kategori yang ada
        $categoryIds = DB::table('categories')->pluck('id')->toArray();

        if (empty($categoryIds)) {
            return; // Jika tidak ada kategori, jangan lanjutkan seeding produk
        }

        foreach (range(1, 20) as $index) {
            DB::table('products')->insert([
                'name'        => $faker->word,
                'imei'        => $faker->numerify('##########'),
                'code'        => strtoupper($faker->bothify('PROD-##??')),
                'is_favorite' => $faker->boolean,
                'desc'        => $faker->sentence,
                'category_id' => $faker->randomElement($categoryIds), // Ambil ID kategori secara acak
            ]);
        }
    }
}
