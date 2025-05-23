{{-- filepath: c:\xampp\htdocs\poketek\resources\views\profile\edit.blade.php --}}
<!DOCTYPE html>
<html lang="es">

<head>
    @vite(['resources/js/app.js'])
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>
    @vite(['resources/js/app.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body>
    @include('layouts.header')

    <div class="container pokemon-detail-container" style="margin-top: 0;">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="row g-4 my-5">
                    <!-- Información de perfil -->
                    <div class="col-12 col-md-6">
                        <div class="bento-card h-100 p-4">
                            <h4 class="mb-3" style="color: var(--golden-bloom);">Datos de perfil</h4>
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
                    <!-- Cambiar contraseña -->
                    <div class="col-12 col-md-6">
                        <div class="bento-card h-100 p-4">
                            <h4 class="mb-3" style="color: var(--golden-bloom);">Cambiar contraseña</h4>
                            <form method="post" action="{{ route('password.update') }}" class="form-poketek">
                                @csrf
                                @method('put')
                                <div class="mb-4">
                                    <label for="update_password_current_password" class="form-label">Contraseña
                                        actual</label>
                                    <input id="update_password_current_password" name="current_password" type="password"
                                        class="form-control shadow-sm" autocomplete="current-password">
                                    @error('updatePassword.current_password')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-4">
                                    <label for="update_password_password" class="form-label">Nueva contraseña</label>
                                    <input id="update_password_password" name="password" type="password"
                                        class="form-control shadow-sm" autocomplete="new-password">
                                    @error('updatePassword.password')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-4">
                                    <label for="update_password_password_confirmation" class="form-label">Confirmar
                                        nueva contraseña</label>
                                    <input id="update_password_password_confirmation" name="password_confirmation"
                                        type="password" class="form-control shadow-sm" autocomplete="new-password">
                                    @error('updatePassword.password_confirmation')
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
                    <!-- Eliminar cuenta -->
                    <div class="col-12">
                        <div class="bento-card h-100 p-4">
                            <h4 class="mb-3" style="color: var(--golden-bloom);">Eliminar cuenta</h4>
                            <form method="post" action="{{ route('profile.destroy') }}" class="form-poketek">
                                @csrf
                                @method('delete')
                                <div class="mb-4">
                                    <label for="delete_password" class="form-label">Contraseña</label>
                                    <input id="delete_password" name="password" type="password"
                                        class="form-control shadow-sm" placeholder="Introduce tu contraseña">
                                    @error('userDeletion.password')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
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

    <style>
        /* ==============================
   FORMULARIOS
============================== */


        /* Labels */
        form label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 700;
            font-family: var(--font-title);
            color: var(--golden-bloom);
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.9rem;
        }

        /* Inputs, selects, textareas */
        form input[type="text"],
        form input[type="email"],
        form input[type="password"],
        form select,
        form textarea {
            width: 100%;
            padding: 0.6rem 1rem;
            border: 2px solid var(--midnight-steel);
            border-radius: 12px;
            background-color: var(--midnight-abyss-light);
            color: var(--white);
            font-family: var(--font-text);
            font-size: 1rem;
            transition: border-color var(--transition-time), box-shadow var(--transition-time);
            outline: none;
            box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3);
            margin-bottom: 1.2rem;
            letter-spacing: 0.02em;
        }

        /* Focus en inputs */
        form input[type="text"]:focus,
        form input[type="email"]:focus,
        form input[type="password"]:focus,
        form select:focus,
        form textarea:focus {
            border-color: var(--golden-bloom);
            box-shadow: 0 0 8px var(--golden-bloom-light);
            background-color: var(--midnight-steel-light);
            color: var(--white);
        }

        /* Placeholder */
        form ::placeholder {
            color: rgba(255, 255, 255, 0.6);
            font-style: italic;
        }

        /* Botones del formulario */
        form button,
        form input[type="submit"] {
            display: inline-block;
            background-color: var(--golden-bloom);
            color: var(--midnight-abyss);
            border: none;
            padding: 0.75rem 2rem;
            font-family: var(--font-title);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-radius: 12px;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(242, 197, 114, 0.5);
            transition: background-color var(--transition-time), transform var(--transition-time), box-shadow var(--transition-time);
            margin-top: 1rem;
            width: 100%;
        }

        /* Hover en botón */
        form button:hover,
        form input[type="submit"]:hover {
            background-color: var(--golden-bloom-light);
            box-shadow: 0 6px 18px rgba(255, 216, 135, 0.8);
            transform: translateY(-3px);
        }

        /* Mensajes de error o validación */
        .form-error {
            color: #f44336;
            /* rojo suave para error */
            font-size: 0.9rem;
            margin-top: -1rem;
            margin-bottom: 1rem;
            font-family: var(--font-text);
        }

        /* Checkbox y radio buttons */
        form input[type="checkbox"],
        form input[type="radio"] {
            accent-color: var(--golden-bloom);
            margin-right: 0.5rem;
        }

        /* Labels inline para checkbox/radio */
        form label.inline {
            display: flex;
            align-items: center;
            cursor: pointer;
            font-family: var(--font-text);
            font-weight: 600;
            color: var(--white);
            margin-bottom: 0.8rem;
        }

        /* Textareas */
        form textarea {
            resize: vertical;
            min-height: 80px;
        }

        /* Ajuste para formularios pequeños */
        form.small-form {
            max-width: 320px;
        }

        /* Estilo para select con flecha personalizada */
        form select {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-image: url("data:image/svg+xml;charset=US-ASCII,<svg fill='%23F2C572' height='24' viewBox='0 0 24 24' width='24' xmlns='http://www.w3.org/2000/svg'><path d='M7 10l5 5 5-5z'/></svg>");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 1em;
            padding-right: 2.5rem;
        }

        /* Deshabilitado */
        form input:disabled,
        form select:disabled,
        form textarea:disabled,
        form button:disabled {
            background-color: var(--midnight-steel);
            color: rgba(255, 255, 255, 0.4);
            cursor: not-allowed;
            box-shadow: none;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>