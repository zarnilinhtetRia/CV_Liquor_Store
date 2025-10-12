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
        Schema::create('item_variations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id')->nullable();
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            $table->foreignId('brand_variation_id')->nullable();
            $table->string('unit')->nullable();
            $table->string('status', 191)->default('1');
            $table->string('expired_date')->nullable();
            $table->string('product_code')->nullable();
            $table->string('barcode')->nullable();
            $table->string('stock_agent_date')->nullable();
            $table->string('model', 191)->nullable();
            $table->string('colour', 191)->nullable();
            $table->string('size', 191)->nullable();
            $table->string('seater', 191)->nullable();
            $table->string('buy_price')->nullable();
            $table->string('wholesale_price')->nullable();
            $table->string('retail_price')->nullable();
            $table->string('retail_set_price', 191)->nullable();
            $table->string('promotion_retail_unit', 191)->nullable();
            $table->string('promotion_retail_set', 191)->nullable();
            //
            $table->string('cost_price')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_variations');
    }
};
