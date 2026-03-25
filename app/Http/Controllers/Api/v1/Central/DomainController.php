<?php

namespace App\Http\Controllers\Api\v1\Central;

use App\Http\Controllers\Controller;

use App\Models\Domain;
use Illuminate\Support\Str;
use App\Services\DomainVerificationService;
//  use Stancl\Tenancy\Database\Models\Domain;

class DomainController extends Controller
{

   public function index()
    {
        return tenant()->domains()->get();
    }
public function store()
{
    $tenant = tenant();

    $domain = tenancy()->central(function () use ($tenant) {
        return Domain::create([
            'tenant_id' => $tenant->id,
            'domain' => request('domain'),
            'type' => 'custom',
            'status' => 'pending',
        ]);
    });

    return response()->json([
        'message' => 'Add a CNAME record to verify domain',
        'cname' => [
            'host' => request('domain'),
            'points_to' => config('tenancy.verification_cname'),
        ],
    ]);
    //  return response()->json([
    //     'dns' => [
    //         'type' => 'CNAME',
    //         'name' => 'www',
    //         'value' => $tenant->id . '.'
    //     ]
    // ]);
}


public function verify($id, DomainVerificationService $service)
{

    $domain = tenancy()->central(function () use ($id) {
        return Domain::findOrFail($id);
    });

    if ($service->verify($domain)) {
        tenancy()->central(function () use ($domain) {
            $domain->update([
                'status' => 'active',
                'verified_at' => now(),
            ]);
        });

        return response()->json(['verified' => true]);
    }

    return response()->json([
        'verified' => false,
        'message' => 'CNAME not detected yet',
    ], 422);
}



    public function makePrimary($id)
    {
         $domain = tenancy()->central(function () use ($id) {
        return Domain::findOrFail($id);
    });

        $domain->tenant->domains()->update(['is_primary' => false]);

        $domain->update([
            'is_primary' => true,
            'status' => 'active'
        ]);

        return response()->json(['message' => 'Primary updated']);
    }
}
