<?php
namespace App\Http\Controllers\Api\v1\Central;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Plan;
use App\Models\Subscription;
use Stancl\Tenancy\Facades\Tenancy;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Subscription as StripeSubscription;

class SubscriptionController extends Controller
{
    public function subscribe(Request $request)
    {
        $validated = $request->validate([
            'tenant_id' => 'required|uuid|exists:tenants,id',
            'plan_id' => 'required|integer|exists:plans,id',
        ]);

        $tenant = Tenant::findOrFail($validated['tenant_id']);
        $plan = Plan::findOrFail($validated['plan_id']);

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            // Create customer on Stripe if not exists
            if (!$tenant->stripe_customer_id) {
                $customer = Customer::create([
                    'email' => $tenant->email,
                    'name' => $tenant->name,
                ]);
                $tenant->update(['stripe_customer_id' => $customer->id]);
            }

            // Create subscription on Stripe
            $stripeSubscription = StripeSubscription::create([
                'customer' => $tenant->stripe_customer_id,
                'items' => [['price' => $plan->stripe_price_id]],
            ]);

            // Store locally
            $subscription = Subscription::create([
                'tenant_id' => $tenant->id,
                'plan_id' => $plan->id,
                'stripe_subscription_id' => $stripeSubscription->id,
                'status' => 'active',
                'starts_at' => now(),
                'ends_at' => now()->addMonth(), // simple example, can use plan interval
            ]);

            $tenant->update([
                'subscription_status' => 'active',
                'stripe_subscription_id' => $stripeSubscription->id,
                'subscription_ends_at' => $subscription->ends_at,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Subscription created successfully.',
                'subscription' => $subscription,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

//  public function show(Request $request)
//     {
//         $tenant = $request->user(); // assuming tenant is authenticated

//         $subscription = Subscription::with('plan')
//             ->where('tenant_id', $tenant->id)
//             ->latest()
//             ->first();

//         if (!$subscription) {
//             return response()->json(['message' => 'No subscription found'], 404);
//         }

//         return response()->json([
//             'plan_name' => $subscription->plan->name,
//             'status' => $subscription->status,
//             'starts_at' => $subscription->starts_at,
//             'ends_at' => $subscription->ends_at,
//             'is_expired' => now()->greaterThan($subscription->ends_at),
//         ]);
//     }

   public function renew(Request $request)
{
    $tenant = $request->user();
    $subscription = Subscription::where('tenant_id', $tenant->id)->latest()->first();

    if (!$subscription) {
        return response()->json(['error' => 'No active subscription found'], 404);
    }

    $plan = $subscription->plan;

    // Free plan → extend locally
    if ($plan->price == 0) {
        $subscription->update([
            'starts_at' => now(),
            'ends_at' => $plan->interval === 'year'
                ? now()->addYear()
                : now()->addMonth(),
        ]);

        return response()->json([
            'message' => 'Free plan renewed successfully!',
            'new_expiry' => $subscription->ends_at,
        ]);
    }

    // Paid plan → generate Stripe checkout
    \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

    $checkoutSession = \Stripe\Checkout\Session::create([
        'customer' => $tenant->stripe_customer_id,
        'payment_method_types' => ['card'],
        'mode' => 'subscription',
        'line_items' => [[
            'price' => $plan->stripe_price_id,
            'quantity' => 1,
        ]],
        'success_url' => config('app.frontend_url') . '/dashboard?renewal=success',
        'cancel_url' => config('app.frontend_url') . '/dashboard?renewal=cancelled',
    ]);

    return response()->json([
        'checkout_url' => $checkoutSession->url,
    ]);
}


 public function show()
    {
        // Get current tenant ID
        $tenantId = tenancy()->tenant->id;

        // Fetch subscription from CENTRAL DB
        $subscription = Subscription::with('plan')
            ->where('tenant_id', $tenantId)
            ->latest()
            ->first();

        if (!$subscription) {
            return response()->json([
                'message' => 'No active subscription found.',
                'plan' => null,
            ]);
        }

        return response()->json([
            'plan_name' => $subscription->plan->name,
            'status' => $subscription->status,
            'starts_at' => $subscription->starts_at,
            'ends_at' => $subscription->ends_at,
        ]);
    }

}
