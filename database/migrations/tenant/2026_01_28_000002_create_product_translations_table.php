<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
 Schema::create('product_translations', function (Blueprint $table) {
    $table->id();
    $table->foreignId('product_id')->constrained()->cascadeOnDelete();

    $table->string('locale', 5); // en, de, ar

    $table->string('name', 500);
    $table->string('slug');
    $table->longText('description')->nullable();

    $table->unique(['product_id', 'locale']);
    $table->unique(['locale', 'slug']); // SEO safe
});



    }

    public function down(): void {
        Schema::dropIfExists('product_translations');
    }
};
