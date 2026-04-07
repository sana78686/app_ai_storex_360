<?php

namespace App\Http\Controllers\Api\v1\Central;

use App\Http\Controllers\Controller;
use App\Models\Domain;
use App\Services\DomainVerificationService;
use Illuminate\Http\Request;

class DomainController extends Controller
{
    public function index()
    {
        $tenant = tenant();

        $domains = tenancy()->central(function () use ($tenant) {
            return Domain::where('tenant_id', $tenant->id)->orderBy('id')->get();
        });

        return response()->json([
            'domains' => $domains,
            'tenant_base_domain' => config('app.tenant_base_domain') ?: config('app.central_domain'),
        ]);
    }

    public function store(Request $request)
    {
        $tenant = tenant();

        $validated = validator($request->all(), [
            'domain' => ['required', 'string', 'max:191'],
        ])->validate();

        $raw = strtolower(trim($validated['domain']));
        $raw = preg_replace('#^https?://#i', '', $raw);
        $raw = rtrim($raw, '/');
        $raw = trim($raw, '.');

        if ($raw === '') {
            return response()->json(['message' => 'Enter a valid domain.'], 422);
        }

        $base = strtolower(trim((string) (config('app.tenant_base_domain') ?: config('app.central_domain'))));

        $full = $raw;
        $isPlatformSubdomain = false;

        if ($base !== '') {
            $suffix = '.'.$base;

            if (! str_contains($raw, '.')) {
                if (! preg_match('/^[a-z0-9]([a-z0-9-]{0,61}[a-z0-9])?$/', $raw)) {
                    return response()->json(['message' => 'Use only letters, numbers, and hyphens for the subdomain label.'], 422);
                }
                $full = $raw.$suffix;
                $isPlatformSubdomain = true;
            } else {
                if ($raw === $base) {
                    return response()->json(['message' => 'You cannot use the platform apex domain as a store hostname.'], 422);
                }
                if (str_ends_with($raw, $suffix)) {
                    $isPlatformSubdomain = true;
                    $full = $raw;
                }
            }
        } elseif (! str_contains($raw, '.')) {
            return response()->json(['message' => 'Enter a full hostname (for example www.yourstore.com).'], 422);
        }

        if (! preg_match('/^([a-z0-9]([a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z]{2,}$/i', $full)) {
            return response()->json(['message' => 'Invalid hostname.'], 422);
        }

        $type = $isPlatformSubdomain ? 'subdomain' : 'custom';

        $taken = tenancy()->central(function () use ($full) {
            return Domain::where('domain', $full)->exists();
        });

        if ($taken) {
            return response()->json(['message' => 'That hostname is already taken.'], 422);
        }

        $domain = tenancy()->central(function () use ($tenant, $full, $type) {
            return Domain::create([
                'tenant_id' => $tenant->id,
                'domain' => $full,
                'type' => $type,
                'status' => $type === 'subdomain' ? 'active' : 'pending',
                'verified_at' => $type === 'subdomain' ? now() : null,
                'is_primary' => false,
            ]);
        });

        if ($type === 'custom') {
            $target = rtrim((string) config('tenancy.verification_cname'), '.');

            return response()->json([
                'message' => 'Add a CNAME record to verify this domain.',
                'dns' => [
                    'type' => 'CNAME',
                    'name' => $full,
                    'value' => $target,
                ],
                'domain' => $domain,
            ]);
        }

        return response()->json([
            'message' => 'Subdomain added and is ready to use once DNS points to the app (wildcard on your platform domain).',
            'dns' => null,
            'domain' => $domain,
        ]);
    }

    public function verify($id, DomainVerificationService $service)
    {
        $tenant = tenant();

        return tenancy()->central(function () use ($id, $tenant, $service) {
            $domain = Domain::where('id', $id)->where('tenant_id', $tenant->id)->firstOrFail();

            if ($domain->type === 'subdomain') {
                $domain->update([
                    'status' => 'active',
                    'verified_at' => now(),
                ]);

                return response()->json(['verified' => true]);
            }

            if ($service->verify($domain)) {
                $domain->update([
                    'status' => 'active',
                    'verified_at' => now(),
                ]);

                return response()->json(['verified' => true]);
            }

            return response()->json([
                'verified' => false,
                'message' => 'CNAME not detected yet',
            ], 422);
        });
    }

    public function makePrimary($id)
    {
        $tenant = tenant();

        return tenancy()->central(function () use ($id, $tenant) {
            $domain = Domain::where('id', $id)->where('tenant_id', $tenant->id)->firstOrFail();

            if ($domain->status !== 'active') {
                return response()->json(['message' => 'Only active domains can be set as primary.'], 422);
            }

            Domain::where('tenant_id', $tenant->id)->update(['is_primary' => false]);

            $domain->update([
                'is_primary' => true,
                'status' => 'active',
            ]);

            return response()->json(['message' => 'Primary updated']);
        });
    }

    public function destroy($id)
    {
        $tenant = tenant();

        return tenancy()->central(function () use ($id, $tenant) {
            $domain = Domain::where('id', $id)->where('tenant_id', $tenant->id)->firstOrFail();

            if ($domain->is_primary) {
                return response()->json(['message' => 'You cannot delete your primary domain.'], 422);
            }

            $domain->delete();

            return response()->json(['message' => 'Domain removed']);
        });
    }
}
