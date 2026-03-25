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
     Schema::create('plans', function (Blueprint $table) {
    $table->id();

    $table->string('name');
    $table->string('code')->unique(); // basic, pro, enterprise

    $table->integer('price'); // in cents
    $table->string('currency', 10)->default('usd');
    $table->string('interval'); // month, year

    $table->integer('trial_days')->default(0);

    $table->json('features')->nullable(); // feature limits

    $table->boolean('is_active')->default(true);

    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
