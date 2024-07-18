<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />

    <meta name="application-name" content="{{ config('app.name') }}" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>{{ config('app.name') }}</title>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    @filamentStyles
    @vite('resources/css/app.css')
    @stack('styles')
</head>

<body class="antialiased">
    @auth
    <x-impersonate::banner style='light' />
    @endauth


    {{ $slot }}


    @filamentScripts
    {{-- @vite('resources/js/app.js') --}}

    @auth
    @livewire('notifications')
    @livewire('database-notifications')
    @endauth

    @stack('scripts')
</body>

</html>
