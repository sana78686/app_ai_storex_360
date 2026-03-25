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
       Schema::create('order_negotiations', function (Blueprint $table) {
    $table->id();

    $table->foreignId('order_id')->constrained()->cascadeOnDelete();

    $table->decimal('customer_offer', 12, 2);
    $table->decimal('admin_counter_offer', 12, 2)->nullable();
    $table->decimal('final_price', 12, 2)->nullable();

    $table->enum('status', ['pending','countered','accepted','rejected','expired'])
          ->default('pending');

    $table->timestamp('expires_at')->nullable();

    $table->timestamps();
});


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
