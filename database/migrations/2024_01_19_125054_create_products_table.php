<?php

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
            $table->bigInteger('product_id');
            $table->longText('name');
            $table->longText('slug');
            $table->longText('image');
            $table->bigInteger('price');
            $table->bigInteger('min_price')->nullable();
            $table->bigInteger('max_price')->nullable();
            $table->bigInteger('price_before_discount')->nullable();
            $table->bigInteger('price_min_before_discount')->nullable();
            $table->bigInteger('price_max_before_discount')->nullable();
            $table->bigInteger('stock');
            $table->bigInteger('like');
            $table->bigInteger('discount');
            $table->mediumText('content');
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
