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
        Schema::table('sells', function (Blueprint $table) {
            $table->string('price_category')->after('seater')->nullable();
            $table->string('retail_set_price')->after('price_category')->nullable();
            $table->string('promotion_retail_unit')->after('retail_set_price')->nullable();
            $table->string('promotion_retail_set')->after('promotion_retail_unit')->nullable();
            $table->string('retail_unit_price')->after('promotion_retail_set')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sells', function (Blueprint $table) {
            //
        });
    }
};
