<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        @vite('resources/css/app.css')

        <title>{{ $title ?? 'Essen 2022 No Shipping Auction' }}</title>

        @livewireStyles
    </head>
    <body class="antialiased font-sans">
        <div>
            {{ $slot }}
        </div>

        @livewireScripts
    </body>
</html>
