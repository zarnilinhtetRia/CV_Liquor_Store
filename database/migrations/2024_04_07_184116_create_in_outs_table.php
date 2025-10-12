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
        Schema::create('in_outs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->nullable();
            $table->integer('items_id')->nullable();
            $table->string('item_variation_id')->nullable();
            $table->string('quantity')->nullable();
            $table->string('total_quantity')->nullable();
            $table->string('unit')->nullable();
            $table->string('date')->nullable();
            $table->string('remark')->nullable();
            $table->string('in_out')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('in_outs');
    }
};
