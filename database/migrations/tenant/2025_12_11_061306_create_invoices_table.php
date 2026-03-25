<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

            // Customer info
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();

            // Invoice metadata
            $table->string('invoice_number')->unique();
            $table->string('status')->default('paid'); // paid, unpaid, draft, canceled

            // Dates
            $table->date('invoice_date')->nullable();
            $table->date('due_date')->nullable();
            $table->date('delivery_date')->nullable();

            // Financial fields
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);

            // Additional fields
            $table->string('currency', 10)->default('EUR');
            $table->string('payment_method')->nullable();

            // Settings snapshot (for historic accuracy)
            $table->json('billing_address')->nullable();
            $table->json('shipping_address')->nullable();

            // Template used
            $table->foreignId('template_id')->nullable()->constrained('invoice_templates')->nullOnDelete();

            // PDF file
            $table->string('pdf_path')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
