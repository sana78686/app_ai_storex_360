<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {

        if (!Schema::hasTable('tenants')) {
          Schema::create('tenants', function (Blueprint $table) {
    $table->uuid('id')->primary();

    $table->string('email')->unique();
    $table->string('password')->nullable();

    $table->string('name')->nullable();
    $table->string('store_name')->nullable();
    $table->string('phone_no')->nullable();

    $table->string('status')->default('active');


    $table->json('data')->nullable(); // extensible metadata

    $table->timestamps();
});

        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
