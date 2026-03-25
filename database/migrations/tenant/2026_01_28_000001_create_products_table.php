<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
   Schema::create('products', function (Blueprint $table) {
    $table->id();

    $table->string('name');                   // product name
    $table->text('detailed_description')->nullable();  // optional long description

    // Identifiers
    $table->string('sku')->nullable()->index();
    $table->string('barcode')->nullable()->index();

    // Status
    $table->enum('status', ['draft', 'active', 'archived'])->default('draft');

    // Pricing
    $table->decimal('price', 12, 2)->default(0);
    $table->decimal('compare_at_price', 12, 2)->nullable();
    $table->decimal('cost_per_item', 12, 2)->nullable();
    $table->boolean('charge_tax')->default(true);

    // Inventory
    $table->boolean('inventory_tracked')->default(true);
    $table->integer('quantity')->default(0);

    // Seller internal fields
    $table->text('seller_note')->nullable(); // NOT exposed to API
    // $table->string('source_name')->nullable(); // supplier / referral link
    // $table->string('source_url')->nullable(); // supplier / referral link

    $table->softDeletes();
    $table->timestamps();
});


    }

    public function down(): void {
        Schema::dropIfExists('products');
    }
};
