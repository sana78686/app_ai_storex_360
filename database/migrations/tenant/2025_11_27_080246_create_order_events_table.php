<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
       Schema::create('order_events', function (Blueprint $table) {
    $table->id();

    $table->foreignId('order_id')->constrained()->cascadeOnDelete();

    $table->string('event');
    // created
    // payment_succeeded
    // shipped
    // refunded
    // canceled

    $table->json('metadata')->nullable();
    $table->timestamp('occurred_at');

    $table->index(['order_id', 'event']);
});


    }

    public function down()
    {
        Schema::dropIfExists('order_events');
    }
};
