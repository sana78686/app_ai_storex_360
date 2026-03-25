<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// database/migrations/xxxx_xx_xx_create_product_options_table.php

return new class extends Migration {
    public function up(): void
    {
       Schema::create('product_options', function (Blueprint $table) {
    $table->id();

    $table->foreignId('product_id')
        ->constrained()
        ->cascadeOnDelete();

    $table->integer('position')->default(0); // ordering (Size first, Color second)

    $table->timestamps();
});

    }

    public function down(): void
    {
        Schema::dropIfExists('product_options');
    }
};

