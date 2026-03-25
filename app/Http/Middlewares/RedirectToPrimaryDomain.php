<?php
namespace App\Http\Middlewares;
use Closure;
use Stancl\Tenancy\Facades\Tenancy;

class RedirectToPrimaryDomain
{
   public function handle($request, Closure $next)
{
    $tenant = tenant();

    if (!$tenant) {
        return $next($request);
    }

    $primary = $tenant->primaryDomain()?->domain;

    if ($primary && $request->getHost() !== $primary) {
        return redirect()->to("https://$primary");
    }

    return $next($request);
}

}
