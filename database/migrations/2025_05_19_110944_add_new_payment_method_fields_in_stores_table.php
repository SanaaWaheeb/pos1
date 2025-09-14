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
            $table->string('is_free_enabled')->default('off')->after('square_location_id');
            $table->integer('number_of_free_sample')->default(0)->after('is_free_enabled');
            $table->string('method')->default('email')->after('number_of_free_sample');
         
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn([
                'is_free_enabled',
                'number_of_free_sample',
                'method'
            ]);
        });
    }
};
