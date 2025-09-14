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
        Schema::table('stores', function (Blueprint $table) {
            $table->string('is_zabeb_enabled')->default('off')->after('edfapay_callback_url');
            $table->string('zabeb_client_id')->after('is_zabeb_enabled');
            $table->string('zabeb_secret_key')->after('zabeb_client_id');
            $table->string('is_ipay88_enabled')->default('off')->after('zabeb_secret_key');
            $table->string('ipay88_merchant_key')->after('is_ipay88_enabled');
            $table->string('ipay88_merchant_code')->after('ipay88_merchant_key');
            $table->string('is_squareup_enabled')->default('off')->after('ipay88_merchant_code');
            $table->string('squareup_appliction_id')->after('is_squareup_enabled');
            $table->string('squareup_access_token')->after('squareup_appliction_id');
            $table->string('squareup_location_id')->after('squareup_access_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn([
                'is_zabeb_enabled',
                'zabeb_client_id',
                'zabeb_secret_key',
                'is_ipay88_enabled',
                'ipay88_merchant_key',
                'ipay88_merchant_code',
                'is_squareup_enabled',
                'squareup_appliction_id',
                'squareup_access_token',
                'squareup_location_id',
            ]);
        });
    }
};
