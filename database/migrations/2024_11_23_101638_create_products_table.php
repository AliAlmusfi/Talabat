<?php

use App\Models\Location;
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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_ar')->nullable(true)->default(null);
            $table->double('price');
            $table->integer('quantity');
            $table->text('info');
            $table->text('info_ar')->nullable(true)->default(null);
            $table->text('image_url')->nullable(true)->default(null);
            $table->foreignIdFor(Location::class , 'location_id')->constrained('locations')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
