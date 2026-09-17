<?php

use App\Models\Market;
use App\Models\User;
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
        Schema::create('reports', function (Blueprint $table){
            $table->id();
            $table->text('info');
            $table->text('info_ar')->nullable(true)->default(null);
            $table->enum('type' , ['Fake Store' , 'Wrong Address' , 'Fake Products' , 'Invalid Products' , 'Inapropriate name or product' , 'Something else']);
            $table->enum('type_ar' , ['متجر مزيف' , 'عنوان خاطئ' , 'منتجات مزيفة' , 'منتجات غير صالحة' , 'اسم او منتج غير لائق' , 'شيئ اخر']);
            $table->foreignIdFor(User::class , 'user_id')->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignIdFor(Market::class , 'market_id')->constrained('markets')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
