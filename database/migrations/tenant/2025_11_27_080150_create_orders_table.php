<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
      Schema::create('orders', function (Blueprint $table) {
    $table->id();
    $table->uuid('order_number')->unique();

    $table->enum('order_type', ['online', 'manual']);
    $table->enum('fulfillment_type', ['delivery', 'pickup']);

    $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
    $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

    $table->decimal('subtotal', 12, 2);
    $table->decimal('discount', 12, 2)->default(0);
    $table->decimal('tax', 12, 2)->default(0);
    $table->decimal('total', 12, 2);
    $table->decimal('negotiated_total', 12, 2)->nullable();

    $table->enum('negotiation_status', ['none','pending','countered','accepted','rejected','expired'])
          ->default('none');

    $table->enum('status', [
        'draft',
        'negotiation_pending',
        'confirmed',
        'paid',
        'processing',
        'shipped',
        'ready_for_pickup',
        'completed',
        'cancelled',
        'refunded'
    ])->default('draft');

    $table->string('currency', 10)->default('PKR');

    $table->string('checkout_token')->nullable()->unique();
    $table->timestamp('expires_at')->nullable();

    $table->timestamps();

    $table->index(['order_type', 'status']);
});


    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
