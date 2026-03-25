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
        // Update all permissions to use sanctum guard
        DB::table('permissions')->update(['guard_name' => 'sanctum']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert all permissions back to web guard
        DB::table('permissions')->update(['guard_name' => 'web']);
    }
};
