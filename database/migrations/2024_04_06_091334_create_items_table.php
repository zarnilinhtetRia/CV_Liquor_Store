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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->nullable();
            $table->foreignId('brand_id')->nullable();
            $table->string('item_name', 191)->nullable();
            $table->string('brand', 191)->nullable();
            $table->string('item_descriptions', 191)->nullable();
            $table->string('item_category', 191)->nullable();
            $table->string('parent_id', 191)->default('0');
            $table->string('type', 191)->nullable();
            $table->string('stock_type')->nullable();
            $table->string('item_type')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
