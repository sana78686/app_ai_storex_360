@extends('layouts.app')

@section('vite')
{{-- for dynamic theme --}}
    @php
    $defaultTheme = config('themes.storefront.default', 'prism');
    $availableThemes = array_keys(config('themes.storefront.available', ['prism' => 'Prism']));
    $theme = \App\Models\Tenant\GeneralSetting::value('theme') ?? $defaultTheme;
    if (!in_array($theme, $availableThemes, true)) {
        $theme = $defaultTheme;
    }
@endphp

@vite("resources/themes/{$theme}/main.js")
{{-- menual theme --}}
    {{-- @vite('resources/themes/classic/main.js') --}}
<div id="app"></div>

@endsection

@section('body')
    <div id="app"></div>
@endsection
