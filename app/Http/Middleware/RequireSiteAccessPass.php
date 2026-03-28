<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireSiteAccessPass
{
    public function handle(Request $request, Closure $next): Response
    {
        $password = config('site.access_password');

        if ($password === null || $password === '') {
            return $next($request);
        }

        $host = strtolower($request->getHost());
        $central = array_map('strtolower', config('tenancy.central_domains', []));

        if (! in_array($host, $central, true)) {
            return $next($request);
        }

        if ($request->is('site-access')) {
            return $next($request);
        }

        if ($request->session()->get('site_access_granted') === true) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Site access password required. Open the app in a browser and unlock /site-access first.',
            ], 403);
        }

        return redirect()->guest('/site-access');
    }
}
