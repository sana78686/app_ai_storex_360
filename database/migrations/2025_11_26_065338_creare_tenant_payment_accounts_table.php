<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tenant_payment_accounts', function (Blueprint $table) {
    $table->id();

    $table->uuid('tenant_id');
    $table->foreign('tenant_id')
        ->references('id')
        ->on('tenants')
        ->cascadeOnDelete();

    $table->string('gateway')->default('stripe');

    $table->string('account_id')->nullable();
    $table->text('access_token')->nullable();
    $table->text('refresh_token')->nullable();
    $table->string('publishable_key')->nullable();
    $table->string('scope')->nullable();

    $table->timestamp('connected_at')->nullable();

    $table->timestamps();

    $table->unique(['tenant_id', 'gateway']);
});

    }

    public function down()
    {
        Schema::dropIfExists('tenant_payment_accounts');
    }
};

