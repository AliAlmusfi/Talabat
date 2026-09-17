<?php

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->decimal('user_longitude', 10, 7);
            $table->decimal('user_latitude', 10, 7);
            $table->double('total_price');
            $table->enum('status', ['preparing', 'sent', 'recieved', 'canceled']);
            $table->enum('status_ar', ['قيد التحضير', 'تم الارسال', 'تم الاستلام' , 'ملغي']);
            $table->foreignIdFor(User::class, 'user_id');
            $table->timestamps();
        });

        Schema::create('order_product', function (Blueprint $table) {
            $table->id();
            $table->integer('quantity');
            $table->foreignIdFor(Product::class , 'product_id')->constrained('products')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignIdFor(Order::class , 'order_id')->constrained('orders')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
        Schema::dropIfExists('order_product');
    }
};
