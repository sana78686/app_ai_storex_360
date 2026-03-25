<?php
namespace App\Http\Controllers\Api\v1\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\Central\Plan;
use Stancl\Tenancy\Facades\Tenancy;

class SubscriptionController extends Controller
{

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
