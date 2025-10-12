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
        Schema::create('product_lists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->foreign('item_id')->references('id')->on('items');
            $table->unsignedBigInteger('variation_id');
            $table->foreign('variation_id')->references('id')->on('item_variations');
            $table->unsignedBigInteger('brand_id');
            $table->foreign('brand_id')->references('id')->on('brands');
            $table->unsignedBigInteger('warehouse_id');
            $table->foreign('warehouse_id')->references('id')->on('warehouses');
            $table->string('unit_price')->nullable();
            $table->string('cost_price')->nullable();
            $table->string('selling_price')->nullable();
            $table->string('unit')->nullable();
            $table->string('moq')->nullable();
            $table->string('unit_m3')->nullable();
            $table->string('unit_kg')->nullable();
            $table->string('model')->nullable();
            $table->string('colour')->nullable();
            $table->string('size')->nullable();
            $table->string('seater')->nullable();
            $table->string('product_code')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_lists');
    }
};
