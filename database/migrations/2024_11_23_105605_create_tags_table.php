<?php

use App\Models\Market;
use App\Models\Market_tag;
use App\Models\Product;
use App\Models\Product_tag;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_tag_listing', function (Blueprint $table) {
            $table->id();
            $table->string('tag')->unique(true);
            $table->timestamps();
        });
        Schema::create('product_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Product_tag::class, 'tag_id')->constrained('product_tag_listing')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignIdFor(Product::class, 'product_id')->constrained('products')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
        Schema::create('market_tag_listing', function (Blueprint $table) {
            $table->id();
            $table->string('tag')->unique(true);
            $table->timestamps();
        });
        Schema::create('market_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Market_tag::class, 'tag_id')->constrained('market_tag_listing')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignIdFor(Market::class, 'market_id')->constrained('markets')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_tag_listing');
        Schema::dropIfExists('product_tag');
        Schema::dropIfExists('market_tag_listing');
        Schema::dropIfExists('market_tag');
    }
};
