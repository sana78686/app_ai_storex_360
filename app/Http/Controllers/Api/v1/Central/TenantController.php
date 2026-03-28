<?php

namespace App\Http\Controllers\Api\v1\Central;

use Carbon\Carbon;
use App\Models\Tenant;
use App\Events\TenantCreated;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\TenantWelcomeEmail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminTenantNotification;
use App\Mail\TenantConfirmationEmail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Process;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Validator;
use App\Models\Plan;
use App\Models\Subscription;
use Stripe\Stripe;
use Stripe\Customer;
use App\Helpers\GeoHelper;
use App\Support\TenantUrl;
use Stancl\Tenancy\Facades\Tenancy;
use App\Models\Tenant\GeneralSetting;
use Stripe\Subscription as StripeSubscription;
use Illuminate\Support\Facades\Cache;








class TenantController extends Controller
{
    public function index()
    {
        try {
            $tenants = Tenant::with('domains')->get();

            // Transform the data to match the frontend expectations
            $formattedTenants = $tenants->map(function ($tenant) {
                return [
                    'id' => $tenant->id,
                    'store_name' => $tenant->store_name,
                    'domain' => $tenant->domains->first()->domain ?? null,
                    'status' => $tenant->status,
                    'created_at' => $tenant->created_at
                ];
            });

            return response()->json([
                'tenants' => $formattedTenants
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching tenants: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to fetch tenants',
                'error' => $e->getMessage()
            ], 500);
        }
    }


 // Tenant model

public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => 'required|email|max:255|unique:tenants,email',
        'password' => 'required|string|min:6',
        'plan_id' => 'nullable|exists:plans,id',
        // 'recaptcha_token' => 'required',
    ]);

    //    $response = Http::asForm()->post(
    //     'https://www.google.com/recaptcha/api/siteverify',
    //     [
    //         'secret' => config('services.recaptcha.secret_key'),
    //         'response' => $request->recaptcha_token,
    //         'remoteip' => $request->ip()
    //     ]
    // );

    // if (!($response->json()['success'] ?? false)) {
    //     return response()->json([
    //         'status' => false,
    //         'message' => 'Captcha validation failed'
    //     ], 422);
    // }

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 422);
    }

    try {
        // Generate unique tenant ID
        do {
            $tenantId = (string) Str::uuid();
        } while (Tenant::where('id', $tenantId)->exists());

      $confirmationToken = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

Cache::put(
    "tenant_otp_$tenantId",
    [
        'token' => $confirmationToken,
        'expires_at' => now()->timestamp
    ],
    1800 // 30 minutes
);

        Log::info('email verification token for tenant', [
            'otp' => $confirmationToken,
            'tenant_id' => $tenantId,
            'email' => $request->email,
        ]);

        if (config('app.debug')) {
            error_log('email verification token for tenant: ' . json_encode([
                'otp' => $confirmationToken,
                'tenant_id' => $tenantId,
                'email' => $request->email,
            ]));
        }

        // ✅ Capture IP ONCE
         $ip = GeoHelper::getClientIP() ?? '176.199.93.89';

        // ✅ Create tenant (Stancl fires events automatically)
        $tenant = Tenant::create([
            'id' => $tenantId,
            'email' => $request->email,
            'password' => $request->password,
            'status' => 'uncomfirmed',
            // 'is_approved' => false,
            // 'confirmation_token' => $confirmationToken,
            // 'confirmation_token_expires_at' => now()->addMinutes(30),

            // ✅ Store IP safely here
            'data' => [
                'ip' => $ip,
            ],
        ]);

        // Create domain
        $domain = "tenant-$tenantId." . config('app.central_domain');
          $tenant->domains()->create([
        'domain' => $domain,
        'is_primary' => true,
        'status' => 'active',
            'verified_at' => now(),
            'type' => 'subdomain',

    ]);

        // Stripe customer (skip if STRIPE_SECRET not configured)
        $stripeSecret = config('services.stripe.secret');
        if (!empty($stripeSecret)) {
            try {
                Stripe::setApiKey($stripeSecret);
                $customer = Customer::create([
                    'email' => $tenant->email,
                    'name' => $tenant->name ?? 'Tenant ' . $tenant->id,
                ]);
                $tenant->update(['stripe_customer_id' => $customer->id]);
            } catch (\Exception $stripeException) {
                // Log Stripe error but don't fail tenant creation
                Log::warning('Stripe customer creation failed', [
                    'error' => $stripeException->getMessage(),
                    'tenant_id' => $tenant->id,
                ]);
            }
        }

        // ✅ Queue-safe mail
        Mail::to($tenant->email)
            ->queue(new \App\Mail\TenantConfirmationEmail($tenant, $confirmationToken));

        return response()->json([
            'message' => 'Tenant created successfully',
            'tenant_id' => $tenantId,
            'tenant_domain' => TenantUrl::to($domain),
        ], 201);

    } catch (\Throwable $e) {
        Log::error('Tenant creation failed', [
            'error' => $e->getMessage(),
        ]);

        return response()->json([
            'error' => 'Tenant creation failed',
            'message' => $e->getMessage(),
        ], 500);
    }
}






    //     public function store(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'email' => 'required|email|max:255|unique:tenants,email',
    //         'password' => 'required|string|min:6',
    //         'name' => 'nullable|string|max:255',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['errors' => $validator->errors()], 422);
    //     }

    //     DB::beginTransaction(); // 🚀 Start central DB transaction

    //     try {
    //         $tenantId = (string) Str::uuid();
    //         $confirmationToken = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

    //         // ✅ Create tenant in central DB
    //         $tenant = Tenant::create([
    //             'id' => $tenantId,
    //             'email' => $request->email,
    //             'password' => Hash::make($request->password),
    //             'status' => 'inactive',
    //             'is_approved' => false,
    //             'confirmation_token' => $confirmationToken,
    //             'confirmation_token_expires_at' => Carbon::now()->addMinutes(30),
    //             'data' => [],
    //         ]);

    //         // ✅ Create domain in central DB
    //         $domain = "$tenantId";
    //         $tenant->domains()->create(['domain' => $domain]);

    //         // ✅ Send confirmation email with OTP (mail failures won’t rollback tenant)
    //         try {
    //             Mail::to($tenant->email)->send(new TenantConfirmationEmail($tenant, $confirmationToken));
    //         } catch (\Exception $mailEx) {
    //             Log::error('Verification email failed: ' . $mailEx->getMessage());
    //         }

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Tenant created successfully. Please check your email for verification code.',
    //             'tenant_id' => $tenantId,
    //             'tenant_domain' => "http://$domain:8000",
    //         ], 201);

    //     } catch (\Exception $e) {
    //         DB::rollBack(); // ❌ Undo central tenant + domain if something failed
    //         Log::error('Tenant creation failed: ' . $e->getMessage());

    //         return response()->json([
    //             'status' => false,
    //             'error' => 'Tenant creation failed',
    //             'message' => $e->getMessage(),
    //         ], 500);
    //     }
    // }


    /**
     * Confirm tenant registration with a token.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
public function confirmTenant(Request $request)
{
    $request->validate([
        'tenant_id' => 'required|string',
        'token' => 'required|string|size:6'
    ]);

    $cacheKey = "tenant_otp_{$request->tenant_id}";

    $otpData = Cache::get($cacheKey);

    if (!$otpData) {
        return response()->json([
            'message' => 'OTP expired or not found. Please request again.'
        ], 400);
    }

    if ($otpData['token'] !== $request->token) {
        return response()->json([
            'message' => 'Invalid confirmation code.'
        ], 400);
    }

    $tenant = Tenant::find($request->tenant_id);

    if (!$tenant) {
        return response()->json(['message' => 'Tenant not found'], 404);
    }

    // ✅ Activate tenant
    $tenant->status = 'trial';
    // $tenant->trial_ends_at = now()->addDays(14);
    $tenant->save();

    // ✅ remove OTP after success
    Cache::forget($cacheKey);

    Mail::to($tenant->email)->queue(new TenantWelcomeEmail($tenant));

    return response()->json([
        'message' => 'Tenant confirmed successfully',
        'tenant_domain' => TenantUrl::to($tenant->domains()->first()->domain, '/dashboard/login'),
    ]);
}



    // resent otp code
public function resendCode(Request $request)
{
    $request->validate([
        'tenant_id' => 'required|string',
    ]);

    $tenant = Tenant::find($request->tenant_id);

    if (!$tenant) {
        return response()->json(['message' => 'Tenant not found'], 404);
    }

    $newToken = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

    Cache::put(
        "tenant_otp_{$tenant->id}",
        [
            'token' => $newToken,
            'expires_at' => now()->timestamp
        ],
        1800
    );

    Log::info('email verification token for tenant', [
        'otp' => $newToken,
        'tenant_id' => $tenant->id,
        'email' => $tenant->email,
    ]);

    if (config('app.debug')) {
        error_log('email verification token for tenant: ' . json_encode([
            'otp' => $newToken,
            'tenant_id' => $tenant->id,
            'email' => $tenant->email,
        ]));
    }

    Mail::to($tenant->email)->queue(new TenantConfirmationEmail($tenant, $newToken));

    return response()->json([
        'message' => 'New confirmation code sent'
    ]);
}



    public function show($id)
    {
        $tenant = Tenant::with('domains')->findOrFail($id);
        return response()->json(['tenant' => $tenant]);
    }

    public function update(Request $request, $id)
    {
        $tenant = Tenant::with('domains')->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'domain' => 'required|string|max:255|unique:domains,domain,' . $tenant->domains->first()?->id,
            'store_name' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::transaction(function () use ($request, $tenant) {
                // Get current data and update it
                $data = $tenant->data;
                $data['name'] = $request->name;
                $data['email'] = $request->email;
                $data['store_name'] = $request->store_name;

                // Update tenant
                $tenant->name = $request->name;
                $tenant->email = $request->email;
                $tenant->store_name = $request->store_name;
                $tenant->data = $data;
                $tenant->save();

                // Update domain if changed
                if ($request->domain !== $tenant->domains->first()?->domain) {
                    $tenant->domains()->update(['domain' => $request->domain]);
                }
            });

            return response()->json([
                'message' => 'Tenant updated successfully',
                'tenant' => $tenant->load('domains')
            ]);

        } catch (\Exception $e) {
            Log::error('Tenant update failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to update tenant',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function approve($id)
    {
        try {
            $tenant = Tenant::findOrFail($id);
            $tenant->approve();
            return response()->json([
                'message' => 'Tenant approved successfully',
                'tenant' => $tenant->load('domains')
            ]);
        } catch (\Exception $e) {
            Log::error('Tenant approval failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to approve tenant',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function block($id)
    {
        try {
            $tenant = Tenant::findOrFail($id);
            $tenant->block();
            return response()->json([
                'message' => 'Tenant blocked successfully',
                'tenant' => $tenant->load('domains')
            ]);
        } catch (\Exception $e) {
            Log::error('Tenant block failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to block tenant',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $tenant = Tenant::findOrFail($id);
            $tenant->delete();
            return response()->json(['message' => 'Tenant deleted successfully'], 204);
        } catch (\Exception $e) {
            Log::error('Tenant deletion failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to delete tenant',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function createTenant(Request $request)
    {
        $email = $request->email;
    }



    public function socialCallback(Request $request)
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

            $accessToken = $tokenResponse->json()['access_token'] ?? null;
            if (!$accessToken) {
                return response()->json(['error' => 'Access token missing from response'], 400);
            }

            // Get user info from Google
            try {
                $socialUser = Socialite::driver('google')->stateless()->userFromToken($accessToken);
                $email = $socialUser->getEmail();
            } catch (\Exception $e) {
                Log::error('Google user info error: ' . $e->getMessage());
                return response()->json(['error' => 'Failed to retrieve user info', 'details' => $e->getMessage()], 400);
            }

            if (!$email) {
                return response()->json(['error' => 'Email not found in Google user info'], 400);
            }

            // Check if tenant exists
            $tenant = Tenant::where('email', $email)->first();

            if (!$tenant) {
                $tenantId = (string) Str::uuid();


                // Generate and log password for debugging
                $plainPassword = Str::random(16);
                Log::info('Generated tenant password', ['plain' => $plainPassword]);

                // Create tenant
                $tenant = Tenant::create([
                    'id' => $tenantId,
                    'email' => $email,
                    'password' => $plainPassword, // Let mutator hash it
                    'status' => 'inactive',
                    'is_approved' => false,
                    'trial_ends_at' => null,
                    'data' => [

                    ],
                ]);



                 // ✅ Create domain
            $domain = "tenant-$tenantId." . config('app.central_domain');
            $tenant->domains()->create(['domain' => $domain]);

                // Create tenant database manually
                // DB::statement("CREATE DATABASE `$databaseName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

                // Initialize tenancy so migrations run in the correct DB
                // tenancy()->initialize($tenant);

                // Run tenant-specific migrations
                // Artisan::call('tenants:migrate', [
                //     '--tenants' => [$tenant->id],
                //     '--force' => true,
                // ]);

                // Optional: Seed the tenant DB
                // Artisan::call('tenants:seed', [
                //     '--tenants' => [$tenant->id],
                //     '--class' => 'TenantDatabaseSeeder', // If you have a specific seeder
                // ]);
            }

            return response()->json([
                'message' => 'Social login successful',
                'tenant_token' => 'dummy_token', // TODO: Replace with real token if available
                'user' => [
                    'email' => $tenant->email,
                    'id' => $tenant->id,
                ],
                'tenant_id' => $tenant->id,
                'tenant_domain' => TenantUrl::to($domain)
            ]);
        } catch (\Exception $e) {
            Log::error('Google login error: ' . $e->getMessage());
            return response()->json(['error' => 'Login failed', 'message' => $e->getMessage()], 500);
        }
    }


}
