<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDomainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('domains', function (Blueprint $table) {
            $table->increments('id');
            $table->string('domain', 191)->unique();
            $table->uuid('tenant_id')->index();
$table->string('verification_token')->nullable();
$table->string('verification_target')->nullable();
// e.g. verify.saletodaystore.app

            $table->boolean('is_primary')->default(false)->index();
         $table->enum('status', [
        'pending',
        'dns_pending',
        'verified',
        'ssl_pending',
        'active',
        'suspended',
    ])->default('pending');
            $table->timestamp('verified_at')->nullable();

 $table->enum('type', ['subdomain','custom'])
          ->default('subdomain');
            $table->timestamps();
            $table->foreign('tenant_id')->references('id')->on('tenants')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('domains');
    }
}
