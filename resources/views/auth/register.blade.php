<x-guest-layout>
    <div class="form-section bento-card"> {{-- Añadido bento-card para el background y la sombra --}}
        <h2 class="text-center mb-4">Crear cuenta</h2>
        <form method="POST" action="{{ route('register') }}" class="form-poketek">
            @csrf

            <div class="mb-3 form-group">
                <x-input-label for="name" :value="__('Nombre')" class="form-label" />
                <x-text-input id="name" class="form-control" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                @error('name')
                    <div class="form-error mt-2">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3 form-group">
                <x-input-label for="email" :value="__('Correo electrónico')" class="form-label" />
                <x-text-input id="email" class="form-control" type="email" name="email" :value="old('email')" required autocomplete="username" />
                @error('email')
                    <div class="form-error mt-2">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3 form-group">
                <x-input-label for="password" :value="__('Contraseña')" class="form-label" />
                <x-text-input id="password" class="form-control"
                                type="password"
                                name="password"
                                required autocomplete="new-password" />
                @error('password')
                    <div class="form-error mt-2">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4 form-group">
                <x-input-label for="password_confirmation" :value="__('Confirmar contraseña')" class="form-label" />
                <x-text-input id="password_confirmation" class="form-control"
                                type="password"
                                name="password_confirmation" required autocomplete="new-password" />
                @error('password_confirmation')
                    <div class="form-error mt-2">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-between align-items-center mt-4">
                <a class="text-decoration-none text-golden-bloom small" href="{{ route('login') }}">
                    {{ __('¿Ya estás registrado?') }}
                </a>

                <x-primary-button class="btn btn-primary px-4 py-2">
                    {{ __('Registrarse') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>