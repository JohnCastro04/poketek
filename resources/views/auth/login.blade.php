<x-guest-layout>
    {{-- Session Status --}}
    <x-auth-session-status class="mb-4 text-center text-golden-bloom" :status="session('status')" />

    {{-- Main form container, applying your form-section style --}}
    <div class="form-section bento-card"> {{-- Añadido bento-card para el background y la sombra --}}
        <h2 class="text-center">¡Bienvenido a Pokétek!</h2>
        <p class="text-center description-card mb-4">Inicia sesión para continuar tu aventura.</p>

        <form method="POST" action="{{ route('login') }}" class="form-poketek">
            @csrf

            {{-- Email Address --}}
            <div class="form-group mb-3"> {{-- Añadido mb-3 para consistencia --}}
                <x-input-label for="email" :value="__('Email')" class="form-label" />
                <x-text-input id="email" class="form-control" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="tu.email@ejemplo.com" />
                <x-input-error :messages="$errors->get('email')" class="form-error" />
            </div>

            {{-- Password --}}
            <div class="form-group mt-4 mb-3"> {{-- Añadido mb-3 para consistencia, manteniendo mt-4 --}}
                <x-input-label for="password" :value="__('Contraseña')" class="form-label" />
                <x-text-input id="password" class="form-control"
                                type="password"
                                name="password"
                                required autocomplete="current-password" placeholder="Ingresa tu contraseña" />
                <x-input-error :messages="$errors->get('password')" class="form-error" />
            </div>

            {{-- Remember Me --}}
            <div class="block mt-4 mb-4"> {{-- Añadido mb-4 para consistencia con el formulario de registro --}}
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" name="remember">
                    <span >{{ __('Recordarme') }}</span>
                </label>
            </div>

            <div class="d-flex justify-content-end align-items-center mt-4"> {{-- Usando d-flex y justify-content-between --}}
                {{-- Login Button --}}
                <button type="submit" class="btn btn-primary px-4 py-2"> {{-- Botón normal --}}
                    {{ __('Iniciar Sesión') }}
                </button>
            </div>

            <div class="flex items-center justify-center mt-4">
                {{-- Register Link --}}
                @if (Route::has('register'))
                    <a class="d-block text-center" href="{{ route('register') }}">
                        {{ __('¿No tienes una cuenta? Regístrate') }}
                    </a>
                @endif
            </div>
        </form>
    </div>
</x-guest-layout>