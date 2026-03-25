<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('mail_settings', function (Blueprint $table) {
            $table->id();

            $table->string('provider')->default('php'); // php, smtp, mailgun, microsoft, sendgrid, sparkpost

            // SMTP fields
            $table->string('smtp_host')->nullable();
            $table->string('smtp_port')->nullable();
            $table->string('smtp_username')->nullable();
            $table->string('smtp_password')->nullable();
            $table->string('smtp_encryption')->nullable(); // ssl, tls

            // Mailgun fields
            $table->string('mailgun_domain')->nullable();
            $table->string('mailgun_api_key')->nullable();
            $table->string('mailgun_region')->nullable(); // EU / US

            // Microsoft fields
            $table->string('microsoft_client_id')->nullable();
            $table->string('microsoft_tenant_id')->nullable();
            $table->string('microsoft_client_secret')->nullable();

            // SendGrid fields
            $table->string('sendgrid_api_key')->nullable();

            // SparkPost fields
            $table->string('sparkpost_api_key')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mail_settings');
    }
};
