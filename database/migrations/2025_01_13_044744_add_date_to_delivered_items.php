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
        Schema::table('delivered_items', function (Blueprint $table) {
            $table->date('date')->nullable()->after('do_no');
            $table->string('remark')->nullable()->after('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivered_items', function (Blueprint $table) {
            $table->dropColumn('date');
            $table->dropColumn('remark');
        });
    }
};
