<?php


namespace App\Http\Controllers\Api\v1\Central;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Crypt;
use Stancl\Tenancy\TenantManager;

use Illuminate\Support\Facades\Log;
use App\Models\Tenant;

class StripeOAuthController extends Controller
{
    public function handleOAuthCallback(Request $request)
    {
        // 1) Handle error response
        if ($request->has('error')) {
            return redirect('/')->with('error', $request->get('error_description') ?? 'Stripe connect canceled');
        }

        $code = $request->get('code');
        $stateEncrypted = $request->get('state');

        if (! $code || ! $stateEncrypted) {
            Log::warning('Stripe OAuth callback missing code or state', $request->all());
            return response('Invalid request', 400);
        }

        // 2) decrypt state to figure out tenant id
        try {
            $stateJson = Crypt::decryptString($stateEncrypted);
            $state = json_decode($stateJson, true);
        } catch (\Throwable $e) {
            Log::error('Failed to decrypt Stripe state', ['err' => $e->getMessage()]);
            return response('Invalid state', 400);
        }

        $tenantId = $state['tenant_id'] ?? null;
        if (! $tenantId) {
            Log::warning('Stripe OAuth callback missing tenant_id in state', ['state' => $state]);
            return response('Invalid state', 400);
        }

        // 3) Initialize tenancy for that tenant so we can update its record (Stancl helper)
        // NOTE: tenancy()->initialize($tenantId) accepts a Tenant object or id depending on your version.
        // We'll fetch the Tenant model from central DB, then initialize.
        $tenant = Tenant::find($tenantId);
        if (! $tenant) {
            Log::error('Tenant not found for Stripe OAuth callback', ['tenant_id' => $tenantId]);
            return response('Tenant not found', 404);
        }

        // We *do not* need to fully initialize DB connection to update central tenants table.
        // We will update the central tenant record (in tenants table) with stripe fields.
        // Exchange code for tokens with Stripe
        $resp = Http::asForm()->post('https://connect.stripe.com/oauth/token', [
            'client_secret' => config('services.stripe.secret'),
            'code' => $code,
            'grant_type' => 'authorization_code',
        ]);

        if (! $resp->successful()) {
            Log::error('Stripe token exchange failed', ['resp' => $resp->body()]);
            return response('Failed to exchange code', 500);
        }

        $data = $resp->json();
// dd($data);
        // Save into tenants table (central)
        $tenant->stripe_account_id = $data['stripe_user_id'] ?? null;
        $tenant->stripe_access_token = isset($data['access_token']) ? Crypt::encryptString($data['access_token']) : null;
        $tenant->stripe_refresh_token = isset($data['refresh_token']) ? Crypt::encryptString($data['refresh_token']) : null;
        $tenant->stripe_publishable_key = $data['stripe_publishable_key'] ?? null;
        $tenant->stripe_scope = $data['scope'] ?? null;
        $tenant->stripe_connected_at = now();
        $tenant->save();
// dd($tenant);
        // You may want to redirect user back to tenant dashboard.
        // Build a redirect URL to the tenant's frontend including a success flag.
        // Use your logic to create tenant URL — e.g. tenant domain stored on tenant model
       $tenantDomain = $tenant->domains()->first()?->domain ?? null;
$redirectUrl = $tenantDomain ? "http://{$tenantDomain}:8000/dashboard/payments?stripe=connected" : '/';


        return redirect($redirectUrl);
    }
}
