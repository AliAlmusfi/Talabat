<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(AdminsAndUsersSeeder::class);
        $this->call(MarketsAndProductsSeeder::class);
        $this->call(OrdersAndReportsSeeder::class);
    }
}
