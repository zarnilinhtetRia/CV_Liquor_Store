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
        Schema::create('transfer_histories', function (Blueprint $table) {
            $table->id();
            $table->string('item_id');
            $table->string('variation_id');
            $table->string('transfer_no')->nullable();
            $table->string('product_name');
            $table->string('from_location', 191);
            $table->string('to_location', 191);
            $table->text('item_name');
            $table->string('date', 191);
            $table->string('quantity', 191);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_histories');
    }
};
