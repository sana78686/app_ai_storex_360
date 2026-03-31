<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use App\Http\Controllers\SiteAccessController;
use App\Http\Middlewares\RedirectToPrimaryDomain;
use App\Http\Middlewares\EnsureDomainIsVerified;

// --------------------------------------------------
// CENTRAL (landlord)
// --------------------------------------------------
foreach (config('tenancy.central_domains') as $domain) {
    Route::domain($domain)->group(function () {
        Route::get('/site-access', [SiteAccessController::class, 'show']);
        Route::post('/site-access', [SiteAccessController::class, 'unlock']);

        Route::get('/{any}', fn () => view('central.index'))
            ->where('any', '^(?!api).*$');
    });
}

// --------------------------------------------------
// TENANT (subdomain)
// --------------------------------------------------
Route::middleware([
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
    RedirectToPrimaryDomain::class,
    EnsureDomainIsVerified::class
])->group(function () {

    // Tenant-only maintenance: run tenant DB seed from tenant domain.
    // Example: https://tenant-<id>.aistorex360.com/db-seed?token=...
    Route::get('/db-seed', function (Request $request) {
        $expected = (string) env('TENANT_SEED_TOKEN', '');
        $provided = (string) $request->query('token', '');

        if ($expected === '' || ! hash_equals($expected, $provided)) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        Artisan::call('db:seed', [
            '--class' => \Database\Seeders\Tenant\TenantDatabaseSeeder::class,
            '--force' => true,
        ]);

        return response()->json([
            'message' => 'Tenant seeded successfully.',
            'tenant_id' => tenant('id'),
            'output' => trim((string) Artisan::output()),
        ]);
    });

    // ⭐ Tenant Admin (dashboard)
    Route::prefix('dashboard')->group(function () {
        Route::get('/{any}', fn () => view('tenant.admin'))
            ->where('any', '.*');
    });

    // ⭐ Storefront (themes)
    Route::get('/{any}', fn () => view('tenant.frontend'))
        ->where('any', '^(?!api|admin).*$');
});
