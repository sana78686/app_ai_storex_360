<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/logo/favicon.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/logo/favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/logo/favicon.png') }}">

    {{-- @vite('resources/css/app.css') --}}

    @yield('vite')
</head>

<body class="antialiased">
    @yield('body')
</body>
</html>
