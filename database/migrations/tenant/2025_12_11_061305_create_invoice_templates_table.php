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
       Schema::create('invoice_templates', function (Blueprint $table) {
    $table->id();


    $table->string('name');
    $table->boolean('is_default')->default(false);

    // Customizable blocks
    $table->text('template_html');   // full body HTML including header/footer
    $table->text('template_css')->nullable();


    // CSS variables / settings
    $table->json('settings')->nullable();

    $table->timestamps();

    // $table->unique(['tenant_id', 'is_default']);
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_templates');
    }
};
