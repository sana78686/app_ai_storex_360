<?php

namespace App\Http\Controllers\Api\v1\Central;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Stripe;
use App\Models\Subscription;
use Stripe\Checkout\Session;
use App\Models\Plan;
use Illuminate\Support\Facades\Auth;
use App\Models\Tenant;

class StripeCheckoutController extends Controller
{
    public function createCheckoutSession(Request $request)
{
    $validated = $request->validate([
        'plan_id' => 'required|exists:central.plans,id',
    ]);

    Stripe::setApiKey(config('services.stripe.secret'));

    $plan = Plan::findOrFail($validated['plan_id']);
    $user = Auth::user();
// $tenantUrl = tenant()->domain;
// $configs = \Stripe\PaymentMethodConfiguration::all([
//     'active' => true,
// ]);
// $enabledMethods = [];

// foreach ($configs->data as $config) {
//     $enabledMethods[] = $config->payment_method_type;
// }
    try {
        // 1️⃣ Create Stripe Checkout session
        $session = \Stripe\Checkout\Session::create([
            // 'payment_method_types' => $enabledMethods,
            'mode' => 'subscription',
            'customer_email' => $user->email ?? null,
            'line_items' => [[
                'price' => $plan->stripe_price_id,
                'quantity' => 1,
            ]],
           // ✅ Redirect directly to tenant dashboard after success
            // 'success_url' => $tenantUrl . ':8000/dashboard/settings?session_id={CHECKOUT_SESSION_ID}',
            // 'cancel_url' => $tenantUrl . ':8000/dashboard/settings?cancelled=true',
             'success_url' => config('app.url') . '/stripe/success?session_id={CHECKOUT_SESSION_ID}',
'cancel_url'  => config('app.url') . '/stripe/cancel',
        ]);

        // 2️⃣ Update local subscription BEFORE redirecting
        Subscription::updateOrCreate(
            [
                'tenant_id' => tenant('id'), // adjust if needed
                // 'user_id' => $user->id,
            ],
            [
                'plan_id' => $plan->id,
                'status' => 'pending', // because payment is not done yet
                'starts_at' => now(),
                'ends_at' => $plan->interval === 'year' ? now()->addYear() : now()->addMonth(),
                'stripe_session_id' => $session->id,
            ]
        );

        // 3️⃣ Return Checkout URL to frontend
        return response()->json([
            'url' => $session->url,
        ]);

    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

}
