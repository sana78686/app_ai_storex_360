
<?php

// declare(strict_types=1);
use Illuminate\Http\Request;
use App\Models\Tenant\GeneralSetting;
use App\Http\Controllers\Api\v1\Tenant\ProductAnalysisController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\Tenant\TenantUserController;
use App\Http\Controllers\Api\v1\Tenant\DashboardController;
use App\Http\Controllers\Api\v1\Central\SubscriptionController;
use App\Http\Controllers\Api\v1\Central\StripeCheckoutController;
// use App\Http\Controllers\Api\v1\Tenant\RoleController;
use App\Http\Controllers\Api\v1\Tenant\TenantPermissionController;
use App\Http\Controllers\Api\v1\Tenant\GeneralSettingsController;
use App\Http\Controllers\Api\v1\Tenant\InvoiceSettingController;
use App\Http\Controllers\Api\v1\Tenant\LegalInfoController;
use App\Http\Controllers\Api\v1\Tenant\OrderController;
use App\Http\Controllers\Api\v1\Tenant\PaymentController;
use App\Http\Controllers\Api\v1\Tenant\ManualOrderController;
use App\Http\Controllers\Api\v1\Tenant\InvoiceTemplateController;
use App\Http\Controllers\Api\v1\Tenant\CategoryController;
use App\Http\Controllers\Api\v1\Tenant\CouponController;
use App\Http\Controllers\Api\v1\Tenant\StripeController;
use App\Http\Controllers\Api\v1\Tenant\MailSettingsController;
use App\Http\Controllers\Api\v1\Tenant\CouponApplyController;
use App\Http\Controllers\Api\v1\Tenant\CheckoutController;
use App\Http\Controllers\Api\v1\Central\ContactController as CentralContactController;
use App\Http\Controllers\Api\v1\Tenant\ProductController;
use App\Http\Controllers\Api\v1\Tenant\CartController;
use App\Http\Controllers\Api\v1\Tenant\CustomerAuthController;
use App\Http\Controllers\Api\v1\Central\DomainController;;

use App\Http\Controllers\PlanController;

// Route::prefix('tenant')->group(function () {
Route::middleware([
    'api',
    \Stancl\Tenancy\Middleware\InitializeTenancyByDomain::class,
    \Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains::class,
    // RedirectToPrimaryDomain::class
])->group(function () {
    // Api for maintenance mode
    // routes/api.php

    Route::apiResource('legal-info', LegalInfoController::class);

    // Route::get('/maintenance-status', function (Request $request) {
    //     $tenant = tenant();
    //     $settings = $tenant ? \DB::table('settings')->first() : null;

    //     $routeName = $request->query('route_name');

    //     $routesUnderMaintenance = [
    //         'dashboard-products',
    //         'settings',
    //         'payments',
    //     ];

    //     $isMaintenance = $settings && $settings->maintenance_mode
    //         && in_array($routeName, $routesUnderMaintenance);

    //     return response()->json([
    //         'maintenance' => $isMaintenance,
    //         'message' => $settings->maintenance_message ?? 'This section is under maintenance',
    //         'logo' => $settings->logo ? asset($settings->logo) : null,
    //     ]);
    // });
    Route::post('/forgot-password', [TenantUserController::class, 'forgot']);
    Route::post('/reset-password', [TenantUserController::class, 'reset']);

    Route::post('/login', [TenantUserController::class, 'Login']);
    Route::post('/signup', [TenantUserController::class, 'Signup']);
    // Route::post('/logout', [TenantUserController::class, 'Logout']);
    Route::post('/contact', [CentralContactController::class, 'store']);

    // Storefront customer auth (register / login)
    Route::post('/customer/register', [CustomerAuthController::class, 'register']);
    Route::post('/customer/login', [CustomerAuthController::class, 'login']);
    Route::middleware('auth:customer')->group(function () {
        Route::get('/customer/me', [CustomerAuthController::class, 'me']);
        Route::post('/customer/logout', [CustomerAuthController::class, 'logout']);
    });
    Route::post('/apply-coupon', [CouponApplyController::class, 'apply']);

    // Cart (guest_token via X-Guest-Token header or body for POST/PUT/DELETE)
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart/items', [CartController::class, 'store']);
    Route::put('/cart/items', [CartController::class, 'update']);
    Route::delete('/cart/items', [CartController::class, 'destroy']);

    // Guest checkout: create customer + order from cart
    Route::post('/checkout/place-order', [CheckoutController::class, 'placeOrder']);
    Route::post('/checkout/set-password', [CheckoutController::class, 'setPassword']);

    Route::get('/test-log', function () {
        \Log::info('Test log route hit');
        return response()->json(['success' => true]);
    });


    Route::prefix('domains')->group(function () {

        Route::get('/', [DomainController::class, 'index']);

        Route::post('/', [DomainController::class, 'store']);

        Route::post('{domain}/verify', [DomainController::class, 'verify']);

        Route::post('{domain}/primary', [DomainController::class, 'makePrimary']);

        Route::delete('{domain}', [DomainController::class, 'destroy']);
    });

    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoryController::class, 'index']);
        Route::get('/by-slug/{slug}', [CategoryController::class, 'showBySlug']);
        Route::post('/', [CategoryController::class, 'store']);
        Route::get('/{id}', [CategoryController::class, 'show']);
        Route::put('/{id}', [CategoryController::class, 'update']);
        Route::delete('/{id}', [CategoryController::class, 'destroy']);
    });
    Route::apiResource('products', ProductController::class);
    Route::get('/invoice-settings', [InvoiceSettingController::class, 'show']);
    Route::post('/invoice-settings', [InvoiceSettingController::class, 'update']);

    Route::get('/stripe/connect', [StripeController::class, 'redirectToStripe']);
    Route::get('/stripe/status', [StripeController::class, 'status']);
    Route::get('/verifyCheckoutSession', [CheckoutController::class, 'verifyCheckoutSession']);
    Route::post('/invoice-templates/preview', [InvoiceTemplateController::class, 'preview']);
    Route::post('/products/analyze', [ProductAnalysisController::class, 'analyze']);


    Route::middleware('auth:sanctum')->group(function () {
        // Route::post('/invoice-templates/preview', [InvoiceTemplateController::class, 'preview']);
        // Route::post('/products/analyze', [ProductAnalysisController::class, 'analyze']);
        Route::post('/invoice-templates', [InvoiceTemplateController::class, 'store']);
        Route::get('/orders', [OrderController::class, 'index']);
        Route::get('/orders/{id}', [OrderController::class, 'show']);
        Route::get('/settings/general', [GeneralSettingsController::class, 'show']);
        Route::post('/settings/general', [GeneralSettingsController::class, 'update']);
        Route::apiResource('legal-info', LegalInfoController::class);

        Route::get('/settings/mail', [MailSettingsController::class, 'show']);
        Route::post('/settings/mail', [MailSettingsController::class, 'update']);
        Route::post('/settings/mail/test', [MailSettingsController::class, 'test']);

        Route::get('/my-subscription', [SubscriptionController::class, 'show']);

        Route::post('/create-checkout-session', [StripeCheckoutController::class, 'createCheckoutSession']);
        // routes/tenant.php
        Route::post('/checkout/create-session', [CheckoutController::class, 'createSession']);

        Route::resource('coupons', CouponController::class);

        // List all plans
        Route::get('/plans', [PlanController::class, 'index']);
        // Create a new plan
        Route::post('/plans', [PlanController::class, 'store']);
        // Update a plan
        Route::put('/plans/{id}', [PlanController::class, 'update']);
        // Delete a plan
        Route::delete('/plans/{id}', [PlanController::class, 'destroy']);





        // web.php (tenant subdomain group)












        Route::get('/dashboard', [DashboardController::class, 'index']);
        Route::post('/logout', [TenantUserController::class, 'Logout']);
        Route::get('/check-token', [TenantUserController::class, 'checkToken']);

        // Route::apiResource('tenants', TenantController::class);

        Route::prefix('users')->group(function () {
            Route::get('/', [TenantUserController::class, 'getAllUsers']);
            Route::put('/{id}', [TenantUserController::class, 'update']);
            Route::get('/roles', [TenantUserController::class, 'getAllRoles']);
        });

        // Route::prefix('roles')->group(function () {
        //     Route::get('/', [RoleController::class, 'index']);
        //     Route::post('/', [RoleController::class, 'store']);
        //     Route::get('/{id}', [RoleController::class, 'show']);
        //     Route::put('/{id}', [RoleController::class, 'update']);
        //     Route::delete('/{id}', [RoleController::class, 'destroy']);
        // });

        Route::prefix('permissions')->group(function () {
            Route::get('/', [TenantPermissionController::class, 'index']);
            Route::post('/', [TenantPermissionController::class, 'store']);
            Route::get('/{id}', [TenantPermissionController::class, 'show']);
            Route::put('/{id}', [TenantPermissionController::class, 'update']);
            Route::delete('/{id}', [TenantPermissionController::class, 'destroy']);
        });
        // Route::prefix('categories')->group(function () {
        //     Route::get('/', [CategoryController::class, 'index']);
        //     Route::post('/', [CategoryController::class, 'store']);
        //     Route::get('/{id}', [CategoryController::class, 'show']);
        //     Route::put('/{id}', [CategoryController::class, 'update']);
        //     Route::delete('/{id}', [CategoryController::class, 'destroy']);
        // });
        Route::patch('products/{id}/status', [ProductController::class, 'updateStatus']);

        Route::get('/products/export', [ProductController::class, 'export']);
        Route::post('/products/import', [ProductController::class, 'import']);
        Route::get('/products/import/status', [ProductController::class, 'checkImportStatus']);
        // Route::apiResource('products', ProductController::class);
        Route::apiResource('attributes', \App\Http\Controllers\Api\v1\Tenant\AttributeController::class);
        Route::apiResource('roles', \App\Http\Controllers\Api\v1\Tenant\TenantRoleController::class);
        Route::prefix('brands')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\v1\Tenant\BrandController::class, 'index']);
        });

         Route::post('/checkout/start', [CheckoutController::class, 'start']);
    Route::post('/checkout/{order}/negotiate', [CheckoutController::class, 'negotiate']);
    Route::post('/checkout/{order}/pay', [CheckoutController::class, 'pay']);

    // Payment webhook
    Route::post('/payment/webhook', [PaymentController::class, 'webhook']);

    // View order
    Route::get('/orders/{order}', [OrderController::class, 'show']);

    // Admin manual orders

        Route::post('/orders', [ManualOrderController::class, 'store']);
        Route::post('/orders/{order}/pay', [ManualOrderController::class, 'pay']);


        // Route::post('products/{product}/variants', [ProductVariantController::class, 'store']);
        // Route::put('variants/{variant}', [ProductVariantController::class, 'update']);
        // Route::delete('variants/{variant}', [ProductVariantController::class, 'destroy']);
        // Category resource routes
        // Route::apiResource('categories', \App\Http\Controllers\Api\v1\Tenant\CategoryController::class);


        // routes/api.php


        Route::post('/settings/theme', function (Request $request) {
            $request->validate([
                'theme' => 'required|in:classic,modern,prism',
            ]);

            $settings = GeneralSetting::firstOrCreate([]);

            $settings->update([
                'theme' => $request->theme,
            ]);

            return response()->json([
                'success' => true,
                'theme' => $settings->theme,
            ]);
        });
    });
    // Customer-authenticated routes (storefront; use /customer/me and auth:customer above)


    Route::fallback(function () {
        return response()->json([
            'success' => false,
            'message' => 'API endpoint not found.',
        ], 404);
    });
});
// Add more tenant-specific API routes here
// });

// Route::middleware([
//     'web',
//     InitializeTenancyByDomain::class,
//     PreventAccessFromCentralDomains::class,
// ])->group(function () {
//     Route::get('/', function () {
//         return 'This is your multi-tenant application. The id of the current tenant is ' . tenant('id');
//     });
// });
