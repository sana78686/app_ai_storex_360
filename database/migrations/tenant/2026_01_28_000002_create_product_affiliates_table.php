<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
  Schema::create('product_affiliates', function (Blueprint $table) {
    $table->id();

    $table->foreignId('product_id')->constrained()->cascadeOnDelete();

    $table->string('platform'); // Amazon, eBay, Daraz
    $table->string('affiliate_url'); // tracking link

    $table->decimal('commission_percent', 5, 2)->nullable();
    $table->boolean('is_active')->default(true);

    $table->timestamps();
});



    }

    public function down(): void {
        Schema::dropIfExists('product_affiliates');
    }
};
