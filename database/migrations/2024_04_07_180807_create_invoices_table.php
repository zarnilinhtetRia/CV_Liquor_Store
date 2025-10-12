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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('customer_id')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('invoice_no', 191)->nullable();
            $table->string('branch', 191)->nullable();
            $table->string('invoice_category', 191)->nullable();
            $table->string('customer_name', 191)->nullable();
            $table->string('phno', 191)->nullable();
            $table->string('sale_by', 191)->nullable();
            $table->string('address', 191)->nullable();
            $table->string('total', 191)->nullable();
            $table->string('status', 191)->nullable();
            $table->string('discount_total', 191)->nullable();
            $table->string('quote_no', 191)->nullable();
            $table->string('invoice_date', 191)->nullable();
            $table->string('overdue_date', 191)->nullable();
            $table->string('quote_date', 191)->nullable();
            $table->string('retail_price', 191)->nullable();
            $table->string('sale_price_category', 191)->nullable();
            $table->string('type', 191)->nullable();
            $table->string('remark', 191)->nullable();
            $table->string('balance_due', 191)->nullable();
            $table->string('sub_total', 191)->nullable();
            $table->string('total_buy_price')->nullable();
            $table->string('deposit', 191)->nullable();
            $table->string('remain_balance', 191)->nullable();
            $table->string('payment_method', 191)->nullable();
            $table->string('location', 191)->nullable();
            //
            $table->string('currency_method', 191)->nullable();
            $table->string('exchange_rate', 191)->nullable();
            $table->string('overall_discount_mmk',191)->nullable();
            $table->string('delivery_status',191)->nullable();
            $table->string('do_no',191)->nullable();
            $table->string('delivery_date',191)->nullable();
            $table->string('branch_invoice_no')->nullable(); //add new column

            $table->softDeletes();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
