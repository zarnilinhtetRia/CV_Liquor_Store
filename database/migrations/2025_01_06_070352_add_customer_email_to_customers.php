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
        Schema::table('customers', function (Blueprint $table) {

            // POS & Transaction Details
            $table->string('pos')->nullable();
            $table->string('date')->nullable();
            $table->string('time')->nullable();
            $table->string('cashier_id')->nullable();
            $table->string('cashier_name')->nullable();
            $table->string('receipt_no')->nullable();
            $table->string('transaction_no')->nullable();
            $table->string('reprinted_by')->nullable();
            $table->string('reprinted_datetime')->nullable();

            // Passenger / Travel Info
            $table->string('passport_no')->nullable();
            $table->string('nationality')->nullable();
            $table->string('flight_code')->nullable();

            // Item / Purchase Details
            $table->string('item_name')->nullable();
            $table->integer('qty')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('discount', 10, 2)->nullable();
            $table->decimal('total', 10, 2)->nullable();

            // Summary Fields
            $table->decimal('sub_total', 10, 2)->nullable();
            $table->decimal('gst', 10, 2)->nullable();
            $table->integer('total_items')->nullable();
            $table->decimal('total_discount', 10, 2)->nullable();
            $table->decimal('other_disc', 10, 2)->nullable();
            $table->decimal('final_total', 10, 2)->nullable();

            // Payment
            $table->decimal('cash', 10, 2)->nullable();
            $table->decimal('change_back', 10, 2)->nullable();
            $table->decimal('adjust', 10, 2)->nullable();

            // Member Details
            $table->string('member_tier')->nullable();
            $table->string('tier_validity')->nullable();
            $table->string('nett_spend')->nullable();
            $table->string('issued_points')->nullable();

            // Points
            $table->string('py2025_bal')->nullable();
            $table->string('py2025_redeem')->nullable();
            $table->string('py2024_points')->nullable();
            $table->string('barcode')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('email');
        });
    }
};
