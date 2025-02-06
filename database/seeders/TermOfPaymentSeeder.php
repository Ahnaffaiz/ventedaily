<?php

namespace Database\Seeders;

use App\Models\TermOfPayment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TermOfPaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $terms = [
            'Cash', '7 Days', '14 Days', '21 Days', '1 Month'
        ];

        foreach ($terms as $term) {
            TermOfPayment::firstOrCreate([
                'name' => $term
            ]);
        }
    }
}
