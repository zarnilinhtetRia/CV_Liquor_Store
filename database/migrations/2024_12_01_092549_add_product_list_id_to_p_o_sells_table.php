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
            $table->unsignedBigInteger('product_list_id')->after('variation_id');
            $table->foreign('product_list_id')->references('id')->on('product_lists')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('p_o_sells', function (Blueprint $table) {
            //
        });
    }
};
