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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('shop_id')->nullable();
            $table->string('manager_id')->nullable();
            $table->string('order_id')->nullable();
            $table->string('order_status')->nullable();
            $table->string('order_date')->nullable();
            $table->string('sender_id')->nullable();
            $table->string('receiver_id')->nullable();
            $table->string('order_weight')->nullable();
            $table->string('sub_amount')->nullable();
            $table->string('parcel_price')->nullable();
            $table->string('code_price')->nullable();
            $table->string('total_amount')->nullable();
            $table->string('cash_status')->nullable();
            $table->text('note')->nullable();
            $table->string('order_type')->nullable();
            $table->string('order_photo1')->nullable();
            $table->string('order_photo2')->nullable();
            $table->string('order_photo3')->nullable();
            $table->string('order_photo4')->nullable();
            $table->string('order_photo5')->nullable();
            $table->string('order_photo6')->nullable();



            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
