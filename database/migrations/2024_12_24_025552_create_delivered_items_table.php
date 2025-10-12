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
        Schema::create('delivered_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id');
            $table->foreign('invoice_id')->references('id')->on('invoices');
            $table->unsignedBigInteger('sell_id');
            $table->foreign('sell_id')->references('id')->on('sells');
            $table->unsignedBigInteger('item_id');
            $table->foreign('item_id')->references('id')->on('items');
            $table->unsignedBigInteger('variation_id');
            $table->foreign('variation_id')->references('id')->on('item_variations');
            $table->string('do_no')->nullable();
            $table->string('origin_qty')->nullable();
            $table->string('delivered_qty')->nullable();
            // $table->string('to_deliver_qty')->nullable();
            $table->string('delivery_group_no')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivered_items');
    }
};
