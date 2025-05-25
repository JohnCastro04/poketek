@extends('layouts.app')

@section('title', config('app.name') . ' - Confirmar contraseña')

@section('content')
    <div class="container d-flex justify-content-center align-items-center" style="min-height: 60vh;">
        <div class="form-section bento-card" style="max-width: 400px; width:100%;">
            <h2 class="text-center mb-3">Confirmar contraseña</h2>
            <div class="mb-4 text-sm text-light text-center">
                Esta es un área segura de la aplicación. Por favor, confirma tu contraseña antes de continuar.
            </div>

            <form method="POST" action="{{ route('password.confirm') }}" class="form-poketek">
                @csrf

                {{-- Contraseña --}}
                <div class="form-group mb-3">
                    <x-input-label for="password" :value="__('Contraseña')" class="form-label" />
                    <x-text-input id="password" class="form-control" type="password" name="password" required
                        autocomplete="current-password" placeholder="Ingresa tu contraseña" />
                    <x-input-error :messages="$errors->get('password')" class="form-error" />
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4">
                    <a class="text-decoration-none text-golden-bloom small" href="{{ route('login') }}">
                        Volver al inicio de sesión
                    </a>
                    <button type="submit" class="btn btn-primary px-4 py-2">
                        Confirmar
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection