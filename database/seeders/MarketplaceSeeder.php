<?php

namespace Database\Seeders;

use App\Models\Color;
use App\Models\Marketplace;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MarketplaceSeeder extends Seeder
{
    public function run()
    {
        $marketplaces = [
            'Tiktok', 'Shopee', 'Website', 'Whatsapp', 'Reseller'
        ];

        foreach ($marketplaces as $marketplace) {
            Marketplace::firstOrCreate([
                'name' => $marketplace
            ]);
        }
    }
}
