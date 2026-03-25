<?php

namespace Database\Seeders\Tenant;

use Illuminate\Database\Seeder;

class TenantDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            // ProductSeeder::class,
            ProductAttributeSeeder::class,
            RolePermissionSeeder::class,
            BrandSeeder::class,
            ProductSeeder::class,
            ProductVariantSeeder::class,
            // BrandSeeder::class,
            // Add more seeders here
        ]);
    }
}
