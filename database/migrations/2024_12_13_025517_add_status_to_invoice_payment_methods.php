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
        Schema::table('invoice_payment_methods', function (Blueprint $table) {
            //
            $table->string('status', 191)->after('payment_amount')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_payment_methods', function (Blueprint $table) {
            //
            $table->dropColumn('status');
        });
    }
};
