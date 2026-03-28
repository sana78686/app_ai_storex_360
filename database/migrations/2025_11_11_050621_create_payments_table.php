<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
    $table->id();

    $table->foreignId('subscription_id')->constrained('subscriptions')->cascadeOnDelete();

    $table->string('gateway')->default('stripe');
    $table->string('gateway_payment_id')->nullable();

    $table->integer('amount');
    $table->string('currency', 10);

    $table->enum('status', [
        'pending',
        'succeeded',
        'failed',
        'refunded'
    ])->default('pending');

    $table->timestamp('paid_at')->nullable();

    $table->json('payload')->nullable(); // raw gateway response

    $table->timestamps();
});

    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
