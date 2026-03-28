<?php

use Illuminate\Support\Facades\Route;
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

    // ⭐ Tenant Admin (dashboard)
    Route::prefix('dashboard')->group(function () {
        Route::get('/{any}', fn () => view('tenant.admin'))
            ->where('any', '.*');
    });

    // ⭐ Storefront (themes)
    Route::get('/{any}', fn () => view('tenant.frontend'))
        ->where('any', '^(?!api|admin).*$');
});
