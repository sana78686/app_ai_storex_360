<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// database/migrations/xxxx_xx_xx_create_product_options_table.php

return new class extends Migration {
    public function up(): void
    {
      Schema::create('product_option_translations', function (Blueprint $table) {
    $table->id();

    $table->foreignId('product_option_id')
        ->constrained()
        ->cascadeOnDelete();

    $table->string('locale', 5); // en, de, ar
    $table->string('name');      // Color, Farbe, اللون

    $table->unique(['product_option_id', 'locale']);

    $table->timestamps();
});


    }

    public function down(): void
    {
        Schema::dropIfExists('product_option_translations');
    }
};

