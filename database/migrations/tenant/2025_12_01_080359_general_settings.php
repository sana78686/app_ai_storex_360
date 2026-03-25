<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('general_settings', function (Blueprint $table) {
            $table->id();

            // Company Info
            $table->string('company_name')->nullable();
            $table->string('default_email')->nullable();
            $table->string('logo')->nullable();
            $table->text('description')->nullable();

            // Legal Details
            $table->text('legal_details')->nullable();

            // Location Fields
            $table->string('country')->nullable();
            $table->string('country_code')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('zip')->nullable();

            // Geo + IP
            $table->string('ip_address')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();

            // System Settings
            $table->string('currency')->nullable();
            $table->string('calling_code')->nullable();
            $table->string('timezone')->nullable();
            $table->string('date_format')->nullable();
            $table->string('datetime_format')->default('YYYY-MM-DD');

            // Maintenance Mode
            $table->boolean('maintenance_mode')->default(false);
            $table->text('maintenance_message')->nullable();
 $table->string('theme')->default('classic');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('general_settings');
    }
};
