<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <!-- Iconos -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/iconoir@6.6.1/css/iconoir.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Scripts y estilos -->
    @vite(['resources/js/app.js'])

    @stack('styles')
    @stack('scripts-head')
</head>

<body>
    @include('layouts.header')

    <div class="min-h-screen">
        <!-- Page Content -->
        <main>
            @yield('content')
        </main>
    </div>
    <div id="mySidenav" class="sidenav">
        <a href="javascript:void(0)" class="closebtn" id="closeSidenav">&times;</a>
        <a href="{{ route('home') }}">Inicio</a>
        <a href="{{ route('pokedex.index') }}">PokéDex</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
    @include('layouts.footer')
</body>

</html>