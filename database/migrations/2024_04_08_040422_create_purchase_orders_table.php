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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('supplier_id')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('branch', 191)->nullable();
            $table->string('invoice_no', 191)->nullable();
            $table->string('invoice_category', 191)->nullable();
            $table->string('supplier_name', 191)->nullable();
            $table->string('phno', 191)->nullable();
            $table->string('address', 191)->nullable();
            $table->string('total', 191)->nullable();
            $table->string('status', 191)->nullable();
            $table->string('discount_total', 191)->nullable();
            $table->string('quote_no', 191)->nullable();
            $table->string('po_date', 191)->nullable();
            $table->string('overdue_date', 191)->nullable();
            $table->string('type', 191)->nullable();
            $table->string('remark', 191)->nullable();
            $table->string('balance_due', 191)->nullable();
            $table->string('sub_total', 191)->nullable();
            $table->string('deposit', 191)->nullable();
            $table->string('unit', 191)->nullable();
            $table->string('exp_date', 191)->nullable();
            $table->string('remain_balance', 191)->nullable();
            $table->string('payment_method', 191)->nullable();
            //
            $table->string('currency_method', 191)->nullable();
            $table->string('exchange_rate', 191)->nullable();
            $table->string('overall_discount_mmk',191)->nullable();
            $table->string('po_status')->nullable();
            $table->string('internal_register_number')->nullable();
            $table->string('supplier_register_number')->nullable();
            $table->string('container_register_number')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
