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
        Schema::create('p_o_sells', function (Blueprint $table) {
            $table->id();
            $table->string('invoiceid', 191);

            $table->integer('supplier_id')->nullable();
            $table->string('description', 191)->nullable();
            $table->string('product_qty', 191)->nullable();
            $table->string('product_price', 191)->nullable();
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
            $table->string('unit_m3')->nullable();
            $table->string('unit_kg')->nullable();
            $table->string('total_unit_m3')->nullable();
            $table->string('total_unit_kg')->nullable();

            $table->string('product_code')->nullable();
            $table->string('model')->nullable();
            $table->string('colour')->nullable();
            $table->string('size')->nullable();
            $table->string('seater')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('p_o_sells');
    }
};
