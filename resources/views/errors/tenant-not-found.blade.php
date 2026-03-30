<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Store Not Found | Enterprise SaaS</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-[#F9FAFB] flex items-center justify-center min-h-screen p-6">

    <div class="max-w-md w-full text-center">
        <div class="mb-8 inline-flex items-center justify-center w-20 h-20 bg-indigo-50 rounded-full">
            <svg class="w-10 h-10 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
        </div>

        <h1 class="text-3xl font-bold text-gray-900 mb-4 tracking-tight">Store not found</h1>
        <p class="text-gray-600 mb-10 leading-relaxed">
            The store you're looking for doesn't exist or may have been moved. Please check the URL or contact the administrator.
        </p>

        <div class="space-y-3">
            <a href="{{ rtrim(config('app.url'), '/') }}/"
               class="block w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl shadow-lg shadow-indigo-200 transition-all active:scale-95">
                Back to main website
            </a>

            <a href="mailto:{{ config('mail.support_address') }}"
               class="block w-full py-3 px-4 bg-white border border-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-all">
                Contact Support
            </a>
        </div>

        <p class="mt-12 text-sm text-gray-400">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </p>
    </div>

</body>
</html>
