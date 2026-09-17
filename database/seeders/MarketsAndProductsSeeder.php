<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\Market;
use App\Models\Market_tag;
use App\Models\Product;
use App\Models\Product_tag;
use Illuminate\Database\Seeder;

class MarketsAndProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Market::factory(10)->create();
        Market_tag::factory(3)->create();
        Product::factory(10)
            ->recycle(Location::factory()->create())
            ->create();
        Product_tag::factory(3)->create();
    }
}
