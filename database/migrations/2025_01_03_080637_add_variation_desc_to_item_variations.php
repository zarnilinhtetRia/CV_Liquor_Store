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
        Schema::table('item_variations', function (Blueprint $table) {
            $table->string('variation_desc')->nullable()->after('brand_variation_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('item_variations', function (Blueprint $table) {
            $table->dropColumn('variation_desc');
        });
    }
};
