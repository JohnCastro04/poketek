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
                
                @if ($errors->get('email'))
                    <div class="error-container mt-2">
                        @foreach ((array) $errors->get('email') as $message)
                            <div class="error-message">
                                <svg class="error-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="error-text">{{ $message }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Password --}}
            <div class="form-group mt-4 mb-3"> {{-- Añadido mb-3 para consistencia, manteniendo mt-4 --}}
                <x-input-label for="password" :value="__('Contraseña')" class="form-label" />
                <x-text-input id="password" class="form-control"
                                type="password"
                                name="password"
                                required autocomplete="current-password" placeholder="Ingresa tu contraseña" />
                
                @if ($errors->get('password'))
                    <div class="error-container mt-2">
                        @foreach ((array) $errors->get('password') as $message)
                            <div class="error-message">
                                <i class="bi bi-exclamation-diamond error-icon"></i>
                                <span class="error-text">{{ $message }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
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