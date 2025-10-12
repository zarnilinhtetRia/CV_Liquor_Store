<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('sells', function (Blueprint $table) {
            $table->id();
            $table->string('invoiceid')->nullable();


            $table->integer('customer_id')->nullable();
            $table->string('description', 191)->nullable();
            $table->string('product_qty', 191)->nullable();
            $table->string('product_price', 191)->nullable();
            $table->string('retail_price', 191)->nullable();
            $table->string('buy_price')->nullable();
            $table->string('discount', 191)->nullable();
            //
            $table->string('discount_category', 191)->nullable();
            $table->string('discount_amt', 191)->nullable();
            //
            $table->string('unit', 191)->nullable();
            $table->string('exp_date', 191)->nullable();
            $table->text('part_number')->nullable();
            $table->string('warehouse', 191)->nullable();
            $table->string('status')->nullable();
            //
            $table->string('foc')->nullable();
            $table->string('product_code')->nullable();
            $table->string('model')->nullable();
            $table->string('colour')->nullable();
            $table->string('size')->nullable();
            $table->string('seater')->nullable();
            $table->string('delivered_qty')->default('0')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sells');
    }
};
