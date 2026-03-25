<?php

namespace App\Http\Controllers\Api\v1\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // If you want to show tenant info, you can use tenant() helper
        $tenantId = tenant()?->id ?? null;
        return response()->json([
            'message' => 'Welcome to the tenant dashboard!',
            'tenant_id' => $tenantId,
        ]);
    }
}
