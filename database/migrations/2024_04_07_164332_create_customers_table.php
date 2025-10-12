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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('customer_id',191)->nullable();
            $table->string('name', 191)->nullable();
            $table->string('phno', 191)->nullable();
            $table->string('branch', 191)->nullable();
            $table->string('shopname', 191)->nullable();
            $table->string('type', 191)->nullable();
            $table->string('address', 191)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
