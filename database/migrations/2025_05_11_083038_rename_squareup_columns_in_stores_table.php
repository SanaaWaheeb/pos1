<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            ALTER TABLE `stores`
              CHANGE `is_squareup_enabled` `is_square_enabled` 
                VARCHAR(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'off',
              CHANGE `squareup_appliction_id` `square_appliction_id` 
                VARCHAR(191) COLLATE utf8mb4_unicode_ci NULL,
              CHANGE `squareup_access_token` `square_access_token` 
                VARCHAR(191) COLLATE utf8mb4_unicode_ci NULL,
              CHANGE `squareup_location_id` `square_location_id` 
                VARCHAR(191) COLLATE utf8mb4_unicode_ci NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("
            ALTER TABLE `stores`
              CHANGE `is_square_enabled` `is_squareup_enabled` 
                VARCHAR(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'off',
              CHANGE `square_appliction_id` `squareup_appliction_id` 
                VARCHAR(191) COLLATE utf8mb4_unicode_ci NULL,
              CHANGE `square_access_token` `squareup_access_token` 
                VARCHAR(191) COLLATE utf8mb4_unicode_ci NULL,
              CHANGE `square_location_id` `squareup_location_id` 
                VARCHAR(191) COLLATE utf8mb4_unicode_ci NULL
        ");
    }
};
