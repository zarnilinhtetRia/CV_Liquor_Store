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
        Schema::create('purchase_order_make_payments', function (Blueprint $table) {
            $table->id();
            $table->string('po_id')->nullable();
            $table->string('po_no')->nullable();
            $table->string('po_record')->nullable();
            $table->string('payment_date', 191)->nullable();
            $table->string('payment_method', 191)->nullable();
            $table->string('amount', 191)->nullable();
            // $table->string('status', 191)->nullable();
            $table->string('note', 191)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_make_payments');
    }
};
