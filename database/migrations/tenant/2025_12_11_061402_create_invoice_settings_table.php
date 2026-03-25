<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_settings', function (Blueprint $table) {
            $table->id();

            $table->string('prefix')->default('INV');
            $table->integer('next_number')->default(1);

            $table->string('default_currency')->default('EUR');
            $table->string('logo')->nullable();

            $table->text('footer_text')->nullable();
            $table->text('intro_text')->nullable();

            $table->boolean('auto_generate_on_paid')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_settings');
    }
};
