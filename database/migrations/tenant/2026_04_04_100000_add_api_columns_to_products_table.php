<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'track_quantity')) {
                $table->boolean('track_quantity')->default(true);
            }
            if (!Schema::hasColumn('products', 'stock')) {
                $table->unsignedInteger('stock')->default(0);
            }
            if (!Schema::hasColumn('products', 'type')) {
                $table->string('type', 32)->default('simple');
            }
            if (!Schema::hasColumn('products', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }
            if (!Schema::hasColumn('products', 'is_featured')) {
                $table->boolean('is_featured')->default(false);
            }
            if (!Schema::hasColumn('products', 'allow_backorder')) {
                $table->boolean('allow_backorder')->default(false);
            }
            if (!Schema::hasColumn('products', 'brand_id')) {
                $table->foreignId('brand_id')->nullable()->constrained('brands')->nullOnDelete();
            }
        });

        if (Schema::hasColumn('products', 'quantity') && Schema::hasColumn('products', 'stock')) {
            DB::statement('UPDATE products SET stock = quantity WHERE stock = 0 AND quantity IS NOT NULL');
        }
        if (Schema::hasColumn('products', 'inventory_tracked') && Schema::hasColumn('products', 'track_quantity')) {
            DB::statement('UPDATE products SET track_quantity = inventory_tracked');
        }
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'brand_id')) {
                try {
                    $table->dropForeign(['brand_id']);
                } catch (\Throwable) {
                    // SQLite / legacy
                }
            }
        });
        Schema::table('products', function (Blueprint $table) {
            foreach (['track_quantity', 'stock', 'type', 'is_active', 'is_featured', 'allow_backorder', 'brand_id'] as $c) {
                if (Schema::hasColumn('products', $c)) {
                    $table->dropColumn($c);
                }
            }
        });
    }
};
