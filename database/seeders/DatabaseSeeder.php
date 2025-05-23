<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (env('APP_ENV') === 'local') {
            $this->call(ColorSeeder::class);
            $this->call(SizeSeeder::class);
            $this->call(BankSeeder::class);
            $this->call(TermOfPaymentSeeder::class);
            $this->call(SettingSeeder::class);
            $this->call(GroupSeeder::class);
            $this->call(SupplierSeeder::class);
            $this->call(CategorySeeder::class);
            $this->call(ProductSeeder::class);
            $this->call(ProductStockSeeder::class);
            $this->call(CustomerSeeder::class);
            $this->call(MarketplaceSeeder::class);
            $this->call(UserSeeder::class);
        } else {
            $this->call(BankSeeder::class);
            $this->call(TermOfPaymentSeeder::class);
            $this->call(SettingSeeder::class);
            $this->call(GroupSeeder::class);
            $this->call(MarketplaceSeeder::class);
            $this->call(UserSeeder::class);
        }

    }
}
