<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
   Schema::create('media', function (Blueprint $table) {
    $table->id();

    $table->unsignedBigInteger('owner_id');      // polymorphic ID
    $table->string('owner_type');                // polymorphic model

    $table->string('type')->nullable();
    $table->string('entity_id')->nullable();
    $table->string('file_key');
    $table->string('cdn_url');
    $table->string('mime_type')->nullable();
    $table->integer('size')->nullable();
    $table->boolean('is_main')->default(false);

    $table->timestamps();
    $table->softDeletes();
});

}

public function down()
{
    Schema::dropIfExists('media');
}

};
