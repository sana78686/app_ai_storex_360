<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
     Schema::create('payments', function (Blueprint $table) {
    $table->id();

    $table->foreignId('order_id')->constrained()->cascadeOnDelete();

    $table->enum('payment_method', ['online','cash','card','bank_transfer']);
    $table->string('payment_provider')->nullable();
    $table->string('transaction_id')->nullable();

    $table->decimal('amount', 12, 2);

    $table->enum('status', ['pending','paid','failed','refunded'])
          ->default('pending');

    $table->timestamp('paid_at')->nullable();

    $table->json('metadata')->nullable();

    $table->timestamps();
});



    }

    public function down()
    {
        Schema::dropIfExists('order_items');
    }
};
