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
        Schema::table('p_o_sells', function (Blueprint $table) {
            $table->string('product_name')->nullable()->after('part_number');
            $table->unsignedBigInteger('item_id')->nullable()->after('product_name');
            $table->foreign('item_id')->references('id')->on('items');
            $table->unsignedBigInteger('variation_id')->nullable()->after('item_id');
            $table->foreign('variation_id')->references('id')->on('item_variations');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('p_o_sells', function (Blueprint $table) {
            $table->dropColumn('product_name');
            $table->dropColumn('item_id');
            $table->dropColumn('variation_id');
            
        });
    }
};
