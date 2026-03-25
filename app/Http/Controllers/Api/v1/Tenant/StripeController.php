<?php

namespace App\Http\Controllers\Api\v1\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class StripeController extends Controller
{
    public function redirectToStripe(Request $request)
    {
        // ensure user/tenant is authenticated and tenant is initialized (tenancy middleware)
        $tenantId = tenant('id'); // stancl helper

        // create secure state payload to identify tenant in callback
        $payload = [
            'tenant_id' => $tenantId,
            'nonce' => Str::random(32),
            'ts' => now()->timestamp,
        ];

        $state = Crypt::encryptString(json_encode($payload));

        $clientId = config('services.stripe.client_id');
        $redirect = urlencode(config('services.stripe.oauth_redirect'));
        $scope = 'read_write'; // change if needed

        $url = "https://connect.stripe.com/oauth/authorize?response_type=code&client_id={$clientId}&scope={$scope}&redirect_uri={$redirect}&state={$state}";

        // Return the redirect URL (frontend will navigate)
        return response()->json(['redirect_url' => $url]);
    }

     public function status(Request $request)
    {
        $tenant = tenant(); // Stancl helper gives current tenant model

        $connected = !empty($tenant->stripe_account_id) && !empty($tenant->stripe_access_token);

        return response()->json(['connected' => $connected]);
    }
}
