<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>

    {{-- @vite('resources/css/app.css') --}}

    @yield('vite')
</head>

<body class="antialiased">
    @yield('body')
</body>
</html>
