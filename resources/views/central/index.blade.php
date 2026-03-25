@extends('layouts.app')

@section('vite')
    @vite('resources/js/central-app/main.js')
@endsection

@section('body')
    <div id="app"></div>
@endsection
