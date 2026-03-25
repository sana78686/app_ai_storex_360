<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('categoriescopy', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // $table->string('slug')->unique();
            $table->string('shopify_id')->nullable()->unique(); // From taxonomy.json
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('vertical')->nullable(); // Added for taxonomy vertical
            $table->string('prefix')->nullable();   // Added for taxonomy prefix
            $table->integer('level')->default(0);   // Added for hierarchy level
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
