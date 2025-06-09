<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/iconoir@6.6.1/css/iconoir.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    @vite(['resources/js/app.js'])
    @stack('styles')
    @stack('scripts-head')
</head>

<body style="background: var(--midnight-abyss); min-height: 100vh;">
    @include('layouts.header')

    <div class="min-h-screen d-flex align-items-center justify-content-center"
        style="background: var(--midnight-abyss);">
        <main class="w-100 h-100 py-5">
            <div class="container pokemon-detail-container">
                <div class="row justify-content-center">
                    <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                        <div class="p-6">
                            {{ $slot }}
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <div id="mySidenav" class="sidenav">
        <a href="javascript:void(0)" class="closebtn" id="closeSidenav">&times;</a>
        <a href="{{ route('home') }}">Inicio</a>
        <a href="{{ route('pokedex.index') }}">PokéDex</a>
        <a href="{{ route('pokemon.random') }}">Generar</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
    @include('layouts.footer')
</body>

</html>