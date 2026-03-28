<?php

namespace App\Http\Controllers\Api\v1\Central;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Domain;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Http;
use App\Support\TenantUrl;

class TenantSocialiteController extends Controller
{

    /**
     * Redirect to Google or Facebook provider for social login.
     */
    public function redirectToProvider($provider)
    {
        try {
            // Use stateless because APIs don't maintain sessions
            $redirectUrl = Socialite::driver($provider)
                ->stateless()
                ->redirect()
                ->getTargetUrl();

            return response()->json(['url' => $redirectUrl]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Could not generate redirect URL',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle callback after user authenticates with social provider.
     */
    public function handleProviderCallback($provider)
    {
        try {
            // Step 1: Get user details from provider
            $socialUser = Socialite::driver($provider)->stateless()->user();
            $email = $socialUser->getEmail();

            // Step 2: Create or find tenant in central DB
            $tenant = Tenant::firstOrCreate(
                ['email' => $email],
                [
                    'id' => (string) Str::uuid(),
                    'password' => Hash::make(Str::random(16)),
                    'status' => 'inactive',
                    'is_approved' => false,
                    'trial_ends_at' => null,
                    'data' => [
                        'email' => $email,
                        'provider' => $provider,
                    ]
                ]
            );

            // Step 3: Ensure tenant domain exists
            if (!$tenant->domains()->exists()) {
                $tenant->domains()->create([
                    'domain' => 'tenant-' . $tenant->id . '.' . config('app.central_domain'),
                    'is_primary' => true,
                    'status' => 'active',
                    'verified_at' => now(),
                    'type' => 'subdomain',
                ]);
            }

            // Step 4: Initialize tenancy
            tenancy()->initialize($tenant);
            DB::connection('tenant')->reconnect();

            // Step 5: Create user in tenant DB if not exists
            $tenantUser = \App\Models\Tenant\User::on('tenant')->firstOrCreate(
                ['email' => $email],
                [
                    'name' => $socialUser->getName() ?? 'Tenant User',
                    'password' => Hash::make(Str::random(16)),
                ]
            );

            // Step 6: Create Sanctum token
            $token = $tenantUser->createToken('tenant_token')->plainTextToken;

            // Step 7: Return response
            return response()->json([
                'message' => 'Tenant login via social successful.',
                'tenant_id' => $tenant->id,
                'tenant_token' => $token,
                'user' => $tenantUser,
                'tenant_domain' => ($d = $tenant->domains()->first()?->domain)
                    ? TenantUrl::to($d)
                    : null,
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Failed to handle social login: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to authenticate tenant via social login',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // API endpoint to get Google OAuth URL
    public function getGoogleAuthUrl()
    {
        $url = Socialite::driver('google')
            ->stateless()
            ->redirect()
            ->getTargetUrl();

        return response()->json(['url' => $url]);
    }

    // API endpoint to handle Google OAuth callback with code
    public function handleGoogleCallback(Request $request)
    {
        $code = $request->input('code');
        if (!$code) {
            return response()->json(['error' => 'Missing code'], 400);
        }

        try {
            // Exchange code for access token
            $tokenResponse = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'authorization_code',
                'client_id' => config('services.google.client_id'),
                'client_secret' => config('services.google.client_secret'),
                'redirect_uri' => config('services.google.redirect'),
                'code' => $code,
            ]);

            if (!$tokenResponse->ok()) {
                return response()->json(['error' => 'Failed to get access token', 'details' => $tokenResponse->json()], 400);
            }

            $accessToken = $tokenResponse->json()['access_token'];

            $socialUser = Socialite::driver('google')
                ->stateless()
                ->userFromToken($accessToken);

            // Check if tenant exists by email
            $tenant = Tenant::where('email', $socialUser->getEmail())->first();

            if (!$tenant) {
                // Create new tenant following the same pattern as TenantController
                Log::info('Creating new tenant for email: ' . $socialUser->getEmail());
                $dbManager = new \App\Services\TenantDatabaseManager();
                $tenantId = (string) Str::uuid();
                Log::info('Generated tenant ID: ' . $tenantId);
                $databaseName = $dbManager->createDatabase($tenantId);
                Log::info('Created database: ' . $databaseName);

                $createdTenantId = null;
                DB::transaction(function () use ($socialUser, $tenantId, $databaseName, &$createdTenantId) {
                    Log::info('Starting tenant creation process via social login');
                    $tenant = new Tenant();
                    $tenant->id = $tenantId;
                    $tenant->email = $socialUser->getEmail();
                    $tenant->password = Hash::make(Str::random(16));
                    $tenant->status = 'inactive';
                    $tenant->is_approved = false;
                    $tenant->trial_ends_at = null;

                    $tenant->data = [
                        'email' => $socialUser->getEmail(),
                        'is_approved' => false,
                        'status' => 'inactive',
                        'provider' => 'google',
                    ];
                    $tenant->save();

                    $generatedDomain = 'tenant-' . $tenant->id . '.' . config('app.central_domain');
                    $tenant->domains()->create([
                        'domain' => $generatedDomain,
                        'is_primary' => true,
                        'status' => 'active',
                        'verified_at' => now(),
                        'type' => 'subdomain',
                    ]);

                    Log::info('Tenant created successfully via social login: ' . $tenant->id);
                    $createdTenantId = $tenant->id;
                });

                // Retrieve the Tenant model by ID
                $tenant = Tenant::find($createdTenantId);
                Log::info('Running migrations for tenant: ' . $tenant->id);
                $dbManager->runMigrations($tenant, $databaseName);
                Log::info('Running seeder for tenant: ' . $tenant->id);
                $dbManager->runSeeder($tenant, $databaseName);
            }

            // Configure tenant connection and fetch the user
            $dbManager = new \App\Services\TenantDatabaseManager();
            $databaseName = 'tenant_' . $tenant->id;
            Log::info('Configuring tenant connection for database: ' . $databaseName);
            $dbManager->configureTenantConnection($databaseName);

            $tenantUser = \App\Models\tenant\User::on('tenant')->where('email', $tenant->email)->first();
            $token = null;
            if ($tenantUser) {
                $token = $tenantUser->createToken('tenant_token')->plainTextToken;
            }

            $tenantHost = $tenant->domains()->first()?->domain;

            return response()->json([
                'message' => 'Tenant authenticated successfully via social login.',
                'tenant_id' => $tenant->id,
                'tenant_token' => $token,
                'user' => $tenantUser,
                'tenant_domain' => $tenantHost ? TenantUrl::to($tenantHost) : null,
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Failed to handle Google callback: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to authenticate tenant via Google',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
