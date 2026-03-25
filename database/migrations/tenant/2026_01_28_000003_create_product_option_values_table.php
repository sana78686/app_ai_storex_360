<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// database/migrations/xxxx_xx_xx_create_product_option_values_table.php

return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_option_values', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_option_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->string('code'); // RED, XL, COTTON
            $table->timestamps();

            // prevent duplicate code per option
            $table->unique(['product_option_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_option_values');
    }
};



