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
        Schema::table('users', function (Blueprint $table) {
            //
            $table->string('account_id')->nullable()->after('id');
            $table->string('father_name')->nullable()->after('name');
            $table->string('phno')->nullable()->after('name');
            $table->string('address')->nullable()->after('name');
            $table->string('social_media')->nullable()->after('name');
            $table->string('profile_photo1')->nullable();
            $table->string('profile_photo2')->nullable();
            $table->string('profile_photo3')->nullable();
            $table->string('profile_photo4')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->dropColumn('account_id');
            $table->dropColumn('father_name');
            $table->dropColumn('phno');
            $table->dropColumn('address');
            $table->dropColumn('social_media');
            $table->dropColumn('profile_photo1');
            $table->dropColumn('profile_photo2');
            $table->dropColumn('profile_photo3');
            $table->dropColumn('profile_photo4');
            $table->dropColumn('confirm_password');
        });
    }
};
