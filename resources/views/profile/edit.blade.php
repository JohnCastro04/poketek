{{-- filepath: c:\xampp\htdocs\poketek\resources\views\profile\edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Editar Perfil')

@section('content')
<div class="container pokemon-detail-container" style="margin-top: 0;">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="row g-4 my-5">
                <div class="col-12 col-md-6">
                    <div class="bento-card h-100 p-4">
                        <h4 class="mb-3" style="color: var(--golden-bloom);">Datos de perfil</h4>

                        {{-- Mensaje de éxito para Datos de perfil (más discreto) --}}
                        @if (session('status') === 'profile-updated')
                            <p class="text-success small mt-2">
                                <i class="bi bi-check-circle-fill me-1"></i> Información de perfil actualizada.
                            </p>
                        @endif

                        <form method="post" action="{{ route('profile.update') }}" class="form-poketek">
                            @csrf
                            @method('patch')
                            <div class="mb-4">
                                <label for="name" class="form-label">Nombre</label>
                                <input id="name" name="name" type="text" class="form-control shadow-sm"
                                    value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
                                @error('name')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="email" class="form-label">Correo electrónico</label>
                                <input id="email" name="email" type="email" class="form-control shadow-sm"
                                    value="{{ old('email', $user->email) }}" required autocomplete="username">
                                @error('email')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-4 text-end">
                                <button class="btn btn-primary px-4 py-2" type="submit">
                                    <i class="bi bi-save me-2"></i>Guardar cambios
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="bento-card h-100 p-4">
                        <h4 class="mb-3" style="color: var(--golden-bloom);">Cambiar contraseña</h4>

                        {{-- Mensaje de éxito para Cambiar contraseña (más discreto) --}}
                        @if (session('status') === 'password-updated')
                            <p class="text-success small mt-2">
                                <i class="bi bi-check-circle-fill me-1"></i> Contraseña actualizada.
                            </p>
                        @endif

                        <form method="post" action="{{ route('password.update') }}" class="form-poketek">
                            @csrf
                            @method('put')
                            <div class="mb-4">
                                <label for="update_password_current_password" class="form-label">Contraseña actual</label>
                                <input id="update_password_current_password" name="current_password" type="password"
                                    class="form-control shadow-sm" autocomplete="current-password">
                                @error('current_password', 'updatePassword')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="update_password_password" class="form-label">Nueva contraseña</label>
                                <input id="update_password_password" name="password" type="password"
                                    class="form-control shadow-sm" autocomplete="new-password">
                                @error('password', 'updatePassword')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="update_password_password_confirmation" class="form-label">Confirmar nueva contraseña</label>
                                <input id="update_password_password_confirmation" name="password_confirmation"
                                    type="password" class="form-control shadow-sm" autocomplete="new-password">
                                @error('password_confirmation', 'updatePassword')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-4 text-end">
                                <button class="btn btn-primary px-4 py-2" type="submit">
                                    <i class="bi bi-key me-2"></i>Guardar contraseña
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-12">
                    <div class="bento-card h-100 p-4">
                        <h4 class="mb-3" style="color: var(--golden-bloom);">Eliminar cuenta</h4>
                        <p class="text-danger small mb-3">
                            Una vez que tu cuenta es eliminada, todos tus datos y recursos serán borrados permanentemente.
                            Por favor, introduce tu contraseña para confirmar que deseas eliminar tu cuenta de forma permanente.
                        </p>
                        <form method="post" action="{{ route('profile.destroy') }}" class="form-poketek">
                            @csrf
                            @method('delete')
                            <div class="mb-4">
                                <label for="delete_password" class="form-label">Contraseña</label>
                                <input id="delete_password" name="password" type="password"
                                    class="form-control shadow-sm" placeholder="Introduce tu contraseña">
                                @error('password', 'userDeletion')
                                    <div class="text-danger small fw-bold mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <button class="btn btn-danger px-4 py-2 w-100" type="submit">
                                    <i class="bi bi-trash me-2"></i>Eliminar cuenta
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection