@extends('layouts.app')

@section('vite')
{{-- for dynamic theme --}}
    @php
    $theme = \App\Models\Tenant\GeneralSetting::value('theme') ?? 'classic';
@endphp

@vite("resources/themes/{$theme}/main.js")
{{-- menual theme --}}
    {{-- @vite('resources/themes/classic/main.js') --}}
<div id="app"></div>

@endsection

@section('body')
    <div id="app"></div>
@endsection
