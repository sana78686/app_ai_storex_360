<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLegalInfosTable extends Migration
{
    public function up()
    {
        Schema::create('legal_infos', function (Blueprint $table) {
            $table->id();

            $table->string('company_name');
            $table->string('legal_form');
            $table->string('registered_address');    // Google Autocomplete output
            $table->string('authorized_representative');
            $table->string('email');
            $table->string('phone');
            $table->string('registration_court');
            $table->string('commercial_register_number');
            $table->string('vat_id')->nullable();
            $table->boolean('oss')->default(false);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('legal_infos');
    }
}
