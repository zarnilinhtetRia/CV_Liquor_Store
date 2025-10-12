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
        Schema::table('transfer_histories', function (Blueprint $table) {
            $table->string('to_item_id')->nullable()->after('variation_id');
            $table->string('to_variation_id')->nullable()->after('to_item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transfer_histories', function (Blueprint $table) {
            $table->dropColumn('to_item_id');
            $table->dropColumn('to_variation_id');
        });
    }
};
