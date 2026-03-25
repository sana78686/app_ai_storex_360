<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouponsTable extends Migration
{
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();

            // Basic
            $table->string('code')->unique();
            $table->enum('type', ['percentage', 'fixed', 'free_shipping'])->default('percentage');
            $table->decimal('value', 10, 2)->nullable();

            // Recurring
            $table->enum('recurring', ['once', 'repeating', 'forever'])->default('once');
            $table->integer('repeating_months')->nullable();

            // Order Restrictions
            $table->decimal('min_order_amount', 10, 2)->nullable();
            $table->decimal('max_discount', 10, 2)->nullable();

            // Date Range
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();

            // Status
            $table->boolean('active')->default(true);

            // Usage Restrictions
            $table->boolean('first_order_only')->default(false);
            $table->boolean('logged_in_only')->default(false);
            $table->boolean('apply_once_per_order')->default(false);
            $table->boolean('apply_only_if_products_match')->default(false);

            // Limits
            $table->integer('usage_limit')->nullable();
            $table->integer('usage_per_customer')->nullable();

            $table->timestamps();
        });



    }

    public function down()
    {
        Schema::dropIfExists('coupon_category');
        Schema::dropIfExists('coupons');
    }
}
