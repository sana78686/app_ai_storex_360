<?php

namespace App\Http\Middlewares;

use Closure;
use Illuminate\Http\Request;

class EnsureDomainIsVerified
{
    public function handle(Request $request, Closure $next)
    {
        $tenant = tenant();

        if (!$tenant) {
            return $next($request);
        }

        $domain = $tenant->domains()
            ->where('domain', $request->getHost())
            ->first();

        if ($domain && $domain->status !== 'active') {
            abort(403, 'Domain not verified');
        }

        return $next($request);
    }
}
