<?php


use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\Api\v1\Central\RoleController;
use App\Http\Controllers\Api\v1\Central\TenantController;
use App\Http\Controllers\Api\v1\Central\PermissionController;
use App\Http\Controllers\Api\v1\Central\SubscriptionController;
use App\Http\Controllers\Api\v1\Central\StripeOAuthController;
use App\Http\Controllers\Api\v1\Central\StripeWebhookController;
use App\Http\Controllers\PlanController;

use App\Http\Controllers\Api\v1\Central\StripeCheckoutController;
use App\Http\Controllers\Api\v1\Central\CentralUserController;
use App\Http\Controllers\Api\v1\Central\ContactController as CentralContactController;

/*
|--------------------------------------------------------------------------
| API Routes (central + tenant)
|--------------------------------------------------------------------------
*/

//
// ----------------------
// ✅ CENTRAL ROUTES
// ----------------------
Route::prefix('central')->group(function () {
    // Route::get('/auth/social/redirect/{provider}', function ($provider) {
    //     return Socialite::driver($provider)->stateless()->redirect();
    // });

    Route::get('/auth/social/redirect/{provider}', function ($provider) {
        $frontendRedirectUri = config('services.google.redirect'); // 👈 must match your Google Console redirect

        $loginUrl = Socialite::driver($provider)
            ->stateless()
            ->redirectUrl($frontendRedirectUri)
            ->redirect()
            ->getTargetUrl();

        return response()->json([
            'url' => $loginUrl
        ]);
    });



    Route::post('/auth/social/callback/google', [TenantController::class, 'socialCallback']);

    // routes/api.php (or central routes)


    Route::get('/stripe/callback', [StripeOAuthController::class, 'handleOAuthCallback']);
    Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook']);



    // routes/api.php
    Route::post('/checkout/session', [StripeCheckoutController::class, 'createCheckoutSession']);


    Route::post('/subscribe', [SubscriptionController::class, 'subscribe']);
    Route::post('/subscription/renew', [SubscriptionController::class, 'renew']);


    // List all plans
    Route::get('/plans', [PlanController::class, 'index']);

    // Create a new plan
    Route::post('/plans', [PlanController::class, 'store']);

    // Update a plan
    Route::put('/plans/{id}', [PlanController::class, 'update']);

    // Delete a plan
    Route::delete('/plans/{id}', [PlanController::class, 'destroy']);


    // Public
    Route::post('/login', [CentralUserController::class, 'Login']);
    Route::post('/signup', [CentralUserController::class, 'Signup']);
    Route::post('/tenants', [TenantController::class, 'store']);
    Route::post('/contact', [CentralContactController::class, 'store']);
    Route::post('tenants/confirm', [TenantController::class, 'confirmTenant']);
    // resent code
    Route::post('tenants/resend-code', [TenantController::class, 'resendCode']);
    // Protected (Central Admin)
    Route::middleware(['auth:sanctum', 'auth:central'])->group(function () {


        Route::get('/tenants', [TenantController::class, 'index']);
        // Route::get('/dashboard', [DashboardController::class, 'index']);
        Route::post('/logout', [CentralUserController::class, 'Logout']);
        Route::get('/check-token', [CentralUserController::class, 'checkToken']);

        // Route::apiResource('tenants', TenantController::class);

        Route::prefix('users')->group(function () {
            Route::get('/', [CentralUserController::class, 'getAllUsers']);
            Route::put('/{id}', [CentralUserController::class, 'update']);
            Route::get('/roles', [CentralUserController::class, 'getAllRoles']);
        });

        Route::prefix('roles')->group(function () {
            Route::get('/', [RoleController::class, 'index']);
            Route::post('/', [RoleController::class, 'store']);
            Route::get('/{id}', [RoleController::class, 'show']);
            Route::put('/{id}', [RoleController::class, 'update']);
            Route::delete('/{id}', [RoleController::class, 'destroy']);
        });

        Route::prefix('permissions')->group(function () {
            Route::get('/', [PermissionController::class, 'index']);
            Route::post('/', [PermissionController::class, 'store']);
            Route::get('/{id}', [PermissionController::class, 'show']);
            Route::put('/{id}', [PermissionController::class, 'update']);
            Route::delete('/{id}', [PermissionController::class, 'destroy']);
        });
    });
});
