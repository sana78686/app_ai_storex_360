<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Access — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-slate-950 flex items-center justify-center p-6">
    <div class="w-full max-w-sm rounded-2xl bg-slate-900 border border-slate-800 p-8 shadow-xl">
        <h1 class="text-xl font-semibold text-white mb-1">{{ config('app.name') }}</h1>
        <p class="text-slate-400 text-sm mb-6">Enter the site access password to continue.</p>

        <form method="post" action="{{ url('/site-access') }}" class="space-y-4">
            @csrf
            <div>
                <label for="password" class="block text-xs font-medium text-slate-400 mb-1.5">Password</label>
                <input
                    type="password"
                    name="password"
                    id="password"
                    autocomplete="current-password"
                    required
                    autofocus
                    class="w-full rounded-lg bg-slate-800 border border-slate-700 text-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none"
                />
                @error('password')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>
            <button
                type="submit"
                class="w-full rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white font-medium py-2.5 text-sm transition-colors"
            >
                Continue
            </button>
        </form>
    </div>
</body>
</html>
