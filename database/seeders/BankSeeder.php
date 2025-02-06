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
            ["name" => "PT. BANK CIMB NIAGA", "code" => "022", "short_name" => "CIMB"],
            ["name" => "PT. BANK CIMB NIAGA UNIT USAHA SYARIAH", "code" => "730", "short_name" => "CIMB SYARIAH"],
            ["name" => "PT. BNI SYARIAH", "code" => "427", "short_name" => "BNI SYARIAH"],
            ["name" => "PT. BANK BCA SYARIAH", "code" => "536", "short_name" => "BCA SYARIAH"],
            ["name" => "PT. BANK BUKOPIN", "code" => "441", "short_name" => "BUKOPIN"],
            ["name" => "PT. BANK CENTRAL ASIA, TBK", "code" => "014", "short_name" => "BCA"],
            ["name" => "PT. BANK DANAMON INDONESIA", "code" => "011", "short_name" => "DANAMON"],
            ["name" => "PT. BANK DKI", "code" => "111", "short_name" => "BANK DKI"],
            ["name" => "PT. BANK DBS INDONESIA", "code" => "046", "short_name" => "DBS"],
            ["name" => "PT. BANK HSBC INDONESIA", "code" => "087", "short_name" => "HSBC"],
            ["name" => "PT. BANK MANDIRI (PERSERO), TBK", "code" => "008", "short_name" => "MANDIRI"],
            ["name" => "PT. BANK MANDIRI TASPEN POS", "code" => "564", "short_name" => "MANDIRI TASPEN"],
            ["name" => "PT. BANK MAYBANK INDONESIA, TBK", "code" => "016", "short_name" => "MAYBANK"],
            ["name" => "PT. BANK MEGA, TBK", "code" => "426", "short_name" => "MEGA"],
            ["name" => "PT. BANK MUAMALAT INDONESIA, TBK", "code" => "147", "short_name" => "MUAMALAT"],
            ["name" => "PT. BANK NEGARA INDONESIA (PERSERO), TBK", "code" => "009", "short_name" => "BNI"],
            ["name" => "PT. BANK OCBC NISP, TBK", "code" => "028", "short_name" => "OCBC NISP"],
            ["name" => "PT. BANK PERMATA, TBK", "code" => "013", "short_name" => "PERMATA"],
            ["name" => "PT. BANK RAKYAT INDONESIA (PERSERO), TBK", "code" => "002", "short_name" => "BRI"],
            ["name" => "PT. BANK SYARIAH BRI", "code" => "422", "short_name" => "BRI SYARIAH"],
            ["name" => "PT. BANK SYARIAH MANDIRI", "code" => "451", "short_name" => "MANDIRI SYARIAH"],
            ["name" => "PT. BANK TABUNGAN NEGARA (PERSERO), TBK", "code" => "200", "short_name" => "BTN"],
            ["name" => "PT. BANK TABUNGAN PENSIUNAN NASIONAL", "code" => "213", "short_name" => "BTPN"],
            ["name" => "PT. BANK WOORI SAUDARA INDONESIA 1906, TBK", "code" => "212", "short_name" => "BWS"]
        ];

        DB::table('banks')->insert($banks);
    }
}
