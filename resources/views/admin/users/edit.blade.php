@extends('layouts.app')

@section('title', config('app.name') . ' - Editar Usuario')

@section('content')
    <section class="header-section text-center">
        <div class="container">
            <h1 class="display-4">Editar Usuario</h1>
            <p class="description-card">Modifica la información de {{ $user->name }}.</p>
        </div>
    </section>

    <section id="edit-user-form" class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="bento-card">
                    <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label for="name" class="form-label" style="color: var(--golden-bloom);">Nombre</label>
                            <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}"
                                   required {{-- AÑADIDO: campo obligatorio --}}
                                   autofocus autocomplete="name"
                                   class="form-control" style="background-color: var(--midnight-steel-light); color: var(--white); border-color: var(--midnight-steel); font-family: var(--font-text);" />
                            @error('name')
                                <div class="text-danger mt-1" style="font-size: 0.875em; color: #FF6347;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label" style="color: var(--golden-bloom);">Email</label>
                            <input id="email" type="email" {{-- AÑADIDO: type="email" para validación de formato --}}
                                   name="email" value="{{ old('email', $user->email) }}"
                                   required {{-- AÑADIDO: campo obligatorio --}}
                                   autocomplete="username"
                                   class="form-control" style="background-color: var(--midnight-steel-light); color: var(--white); border-color: var(--midnight-steel); font-family: var(--font-text);" />
                            @error('email')
                                <div class="text-danger mt-1" style="font-size: 0.875em; color: #FF6347;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="permission" class="form-label" style="color: var(--golden-bloom);">Permiso de Administrador</label>
                            <select id="permission" name="permission"
                                    required {{-- AÑADIDO: campo obligatorio --}}
                                    class="form-select" style="background-color: var(--midnight-steel-light); color: var(--white); border-color: var(--midnight-steel); font-family: var(--font-text);">
                                <option value="0" {{ old('permission', $user->permission) == 0 ? 'selected' : '' }}>Usuario Normal (0)</option>
                                <option value="1" {{ old('permission', $user->permission) == 1 ? 'selected' : '' }}>Administrador (1)</option>
                            </select>
                            @error('permission')
                                <div class="text-danger mt-1" style="font-size: 0.875em; color: #FF6347;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('admin.dashboard') }}"
                               class="btn"
                               style="background-color: var(--midnight-steel); border: 2px solid var(--golden-bloom); color: var(--golden-bloom); font-weight: 700; text-decoration: none; margin-right: 10px;">
                                Cancelar
                            </a>
                            <button type="submit"
                                    class="btn"
                                    style="background-color: var(--golden-bloom); color: var(--midnight-abyss); font-weight: 700;">
                                Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection