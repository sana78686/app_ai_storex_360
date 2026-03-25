<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// database/migrations/xxxx_xx_xx_create_product_option_values_table.php

return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_option_value_translations', function (Blueprint $table) {
            $table->id();

            // $table->foreignId('product_option_value_id')
            //       ->constrained()
            //       ->cascadeOnDelete();
            $table->foreignId('product_option_value_id')
             ->constrained(table: 'product_option_values', indexName: 'povt_pov_fk')
             ->cascadeOnDelete();

            $table->string('locale', 10); // en, ar, fr
            $table->string('name');       // Red, أحمر, Rouge

            $table->timestamps();

            // prevent duplicate locale per value
           // $table->unique(['product_option_value_id', 'locale']);
           $table->unique(['product_option_value_id', 'locale'], 'povt_id_locale_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_option_value_translations');
    }
};



