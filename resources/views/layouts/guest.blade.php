<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" href="{{asset('images/favicon.svg', config('scaffold.secure'))}}" />
        <title>{{ config('app.name') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400..700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
        <style>
            [x-cloak] {
                display: none !important;
            }
        </style>
        @stack('styles')
        @stack('scripts')
    </head>
    <body class="antialiased min-h-screen">
        {{ $slot }}

        @livewireScripts
        @stack('late-scripts')
    </body>
</html>
