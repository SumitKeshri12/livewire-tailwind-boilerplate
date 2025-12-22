<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>{{ $title ?? config('app.name') }}</title>

    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    @livewireStyles(['nonce' => $nonce])
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @fluxAppearance(['nonce' => $nonce])

    @stack('styles')
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <x-layouts.app.secondary-header />
    <flux:main>
        <!-- Flash Message  -->
        <livewire:flash-message />

        {{ $slot }}
    </flux:main>

    {{-- Flux Scripts --}}
    @livewireScripts(['nonce' => $nonce])
    @fluxScripts(['nonce' => $nonce])

    <script nonce="{{ $nonce }}" src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    @stack('scripts')

</body>

</html>
