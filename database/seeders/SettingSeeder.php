<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('settings')->insert([
                'name' => 'Vente Store',
                'address' => 'Beteng Trade Center Lantai 1 Blok A4 no 14',
                'telp' => '87877396865',
                'owner' => 'Ardhyan Zulfikar Malik',
                'keep_timeout' => '08:00',
                'keep_code' => 'K',
                'keep_increment' => 0,
                'pre_order_code' => 'P',
                'pre_order_increment' => 0,
                'sale_code' => 'A',
                'sale_increment' => 0,
                'retur_code' => 'R',
                'retur_increment' => 0,
                'logo' => null
            ]);
    }
}
