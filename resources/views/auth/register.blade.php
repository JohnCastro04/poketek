<x-guest-layout>
    <div class="form-section ">
        <h2 class="text-center">Crea tu cuenta</h2>
        <p class="text-center description-card mb-4">Regístrate para comenzar tu aventura Pokémon.</p>

        <form method="POST" action="{{ route('register') }}" class="form-poketek">
            @csrf

            {{-- Nombre --}}
            <div class="form-group mb-3">
                <x-input-label for="name" :value="__('Nombre')" class="form-label" />
                <x-text-input id="name" class="form-control" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                <x-form-error :messages="$errors->get('name')" />
            </div>

            {{-- Correo electrónico --}}
            <div class="form-group mt-4 mb-3">
                <x-input-label for="email" :value="__('Correo electrónico')" class="form-label" />
                <x-text-input id="email" class="form-control" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="tu.email@ejemplo.com" />
                <x-form-error :messages="$errors->get('email')" />
            </div>

            {{-- Contraseña con indicador de fuerza --}}
            <div class="form-group mt-4 mb-3">
                <x-input-label for="password" :value="__('Contraseña')" class="form-label" />
                <div style="position: relative;">
                    <x-text-input id="password" class="form-control"
                                  type="password"
                                  name="password"
                                  required autocomplete="new-password"/>
                    <x-password-strength-indicator inputId="password" position="bottom" />
                </div>
                <x-form-error :messages="$errors->get('password')" />
            </div>

            {{-- Confirmar contraseña --}}
            <div class="form-group mt-4 mb-4">
                <x-input-label for="password_confirmation" :value="__('Confirmar contraseña')" class="form-label" />
                <x-text-input id="password_confirmation" class="form-control"
                              type="password"
                              name="password_confirmation" required autocomplete="new-password"/>
                <x-form-error :messages="$errors->get('password_confirmation')" />
            </div>

            <div class="d-flex justify-content-end align-items-center mt-4">
                <button type="submit" class="btn btn-primary px-4 py-2">
                    {{ __('Crear cuenta') }}
                </button>
            </div>

            <div class="flex items-center justify-center mt-4">
                <a class="d-block text-center" href="{{ route('login') }}">
                    {{ __('¿Ya tienes una cuenta? Inicia sesión') }}
                </a>
            </div>
        </form>
    </div>
</x-guest-layout>
