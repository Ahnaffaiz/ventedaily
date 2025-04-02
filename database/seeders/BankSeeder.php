<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $banks = [
            ["name" => "BCA", "code" => "001", "short_name" => "BCA"],
            ["name" => "BRI", "code" => "002", "short_name" => "BRI"],
            ["name" => "BNI", "code" => "003", "short_name" => "BNI"],
            ["name" => "Tiktok", "code" => "004", "short_name" => "Tiktok"],
            ["name" => "Shopee", "code" => "005", "short_name" => "Shopee"],
            ["name" => "Whatsapp", "code" => "006", "short_name" => "Whatsapp"],
            ["name" => "Other", "code" => "007", "short_name" => "Other"],
        ];

        DB::table('banks')->insert($banks);
    }
}
