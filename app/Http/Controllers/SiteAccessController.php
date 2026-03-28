<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class SiteAccessController extends Controller
{
    public function show(): View
    {
        return view('site-access');
    }

    public function unlock(Request $request): RedirectResponse
    {
        $password = config('site.access_password');

        if ($password === null || $password === '') {
            return redirect('/');
        }

        $request->validate([
            'password' => ['required', 'string'],
        ]);

        if (! hash_equals((string) $password, (string) $request->input('password'))) {
            return back()->withErrors(['password' => __('Access denied. Incorrect password.')])->onlyInput('password');
        }

        $request->session()->put('site_access_granted', true);

        return redirect()->intended('/');
    }
}
