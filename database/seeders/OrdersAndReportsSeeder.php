<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\User;
use Database\Factories\OrderProductFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrdersAndReportsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $factory = new OrderProductFactory();

        $product_orders = collect()->times(10, fn () => $factory->definition())->toArray();


        Order::factory(10)
            ->recycle(User::factory()->create())
            ->create();
        DB::table('order_product')->insert($product_orders);
    }
}
