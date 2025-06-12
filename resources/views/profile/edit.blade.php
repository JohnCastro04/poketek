@extends('layouts.app')

@section('title', 'Editar Perfil')

@section('content')
<div class="container pokemon-detail-container">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="row g-4 my-5">
                <div class="col-12 col-md-6">
                    <div class="bento-card h-100 p-4">
                        <h4 class="mb-3 text-golden-bloom">Datos de perfil</h4>

                        {{-- Mensaje de éxito para Datos de perfil --}}
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
                                <button class="btn btn-primary px-4 py-2" type="submit">Guardar cambios</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="bento-card h-100 p-4">
                        <h4 class="mb-3 text-golden-bloom">Cambiar contraseña</h4>

                        {{-- Mensaje de éxito para Cambiar contraseña --}}
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
                                <button class="btn btn-primary px-4 py-2" type="submit">Guardar contraseña</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-12">
                    <div class="bento-card p-4 profile-picture-section">
                        <h4 class="mb-3 text-golden-bloom">Foto de perfil</h4>

                        {{-- Mensaje de éxito para foto de perfil --}}
                        @if (session('status') === 'profile-picture-updated')
                        <p class="text-success small mt-2">
                            <i class="bi bi-check-circle-fill me-1"></i> Foto de perfil actualizada.
                        </p>
                        @endif

                        <div class="row align-items-center">
                            <div class="col-12 col-md-3 text-center mb-3 mb-md-0">
                                <label class="form-label d-block text-light">Foto actual</label>
                                <img id="currentProfilePicture" src="{{ $user->profile_picture_url }}" alt="Foto de perfil actual"
                                    class="rounded-circle shadow-lg current-profile-img">
                            </div>

                            <div class="col-12 col-md-9">
                                <form method="post" action="{{ route('profile.picture.update') }}" class="form-poketek">
                                    @csrf
                                    @method('patch')
                                    
                                    <label class="form-label text-light mb-3">Seleccionar nueva foto</label>
                                    
                                    <div id="profilePictureCarousel" class="carousel slide profile-carousel-custom" data-bs-ride="false">
                                        <div class="carousel-inner">
                                            @php
                                                $chunkedPictures = array_chunk($availableProfilePictures, 6); // 6 imágenes por slide
                                            @endphp
                                            
                                            @foreach($chunkedPictures as $index => $pictureChunk)
                                            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                                <div class="d-flex justify-content-center align-items-center flex-wrap gap-3 py-3">
                                                    @foreach($pictureChunk as $pictureId)
                                                    <label class="profile-picture-option">
                                                        <input type="radio" name="profile_picture" value="{{ $pictureId }}"
                                                            {{ $user->profile_picture == $pictureId ? 'checked' : '' }}
                                                            class="d-none profile-radio">
                                                        <img src="{{ asset("images/profile/{$pictureId}.png") }}"
                                                            alt="Opción {{ $pictureId }}"
                                                            class="profile-picture-selector rounded-circle"
                                                            data-picture-id="{{ $pictureId }}">
                                                    </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                        
                                        <div class="carousel-indicators carousel-indicators-custom">
                                            @foreach($chunkedPictures as $index => $chunk)
                                            <a type="button" data-bs-target="#profilePictureCarousel" data-bs-slide-to="{{ $index }}" 
                                                {{ $index === 0 ? 'class=active aria-current=true' : '' }} 
                                                aria-label="Slide {{ $index + 1 }}"></a>
                                            @endforeach
                                        </div>
                                    </div>

                                    @error('profile_picture')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                    @enderror

                                    <div class="text-end mt-4">
                                        <button class="btn btn-primary px-4 py-2 btn-save-profile" type="submit">Cambiar foto</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="bento-card p-4 delete-account-section">
                        <h4 class="mb-3 text-golden-bloom">Eliminar cuenta</h4>
                        <p class="text-danger small mb-3">
                            Una vez que tu cuenta es eliminada, todos tus datos y recursos serán borrados permanentemente.
                            Por favor, introduce tu contraseña para confirmar que deseas eliminar tu cuenta de forma permanente.
                        </p>
                        <form method="post" action="{{ route('profile.destroy') }}" class="form-poketek">
                            @csrf
                            @method('delete')
                            <div class="mb-4">
                                <label for="delete_password" class="form-label text-light">Contraseña</label>
                                <input id="delete_password" name="password" type="password"
                                    class="form-control shadow-sm" placeholder="Introduce tu contraseña">
                                @error('password', 'userDeletion')
                                <div class="text-danger small fw-bold mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <button class="btn btn-danger px-4 py-2 w-100" type="submit">Eliminar cuenta</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    :root {
        --golden-bloom: #F2C572;
        --golden-bloom-light: #FFD89C;
        --midnight-abyss: #121621;
        --midnight-abyss-light: #1A2030;
        --midnight-steel: #2C3850;
        --midnight-steel-light: #3A4A68;
    }

    /* Estilos generales para las tarjetas bento */
    .bento-card {
        background-color: var(--midnight-abyss-light);
        border-radius: 15px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
        color: var(--golden-bloom-light);
        transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
        border: 1px solid var(--midnight-steel);
    }

    .bento-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 25px rgba(0, 0, 0, 0.5);
    }

    /* Títulos dentro de las tarjetas */
    .bento-card h4 {
        color: var(--golden-bloom);
        font-weight: bold;
        border-bottom: 2px solid var(--midnight-steel);
        padding-bottom: 10px;
        margin-bottom: 20px;
    }

    /* Colores para el texto */
    .text-golden-bloom {
        color: var(--golden-bloom) !important;
    }

    .text-success {
        color: var(--golden-bloom-light) !important; /* Usar un color de la paleta para mensajes de éxito */
    }

    .text-danger {
        color: #dc3545 !important; /* Mantener un color rojo estándar para peligro */
    }

    /* Estilos para los formularios */
    .form-label {
        color: var(--golden-bloom-light);
        font-weight: 500;
    }

    .form-control {
        background-color: var(--midnight-steel);
        color: var(--golden-bloom-light);
        border: 1px solid var(--midnight-steel-light);
        padding: 0.75rem 1rem;
        border-radius: 8px;
    }

    .form-control::placeholder {
        color: var(--golden-bloom-light);
        opacity: 0.7;
    }

    .form-control:focus {
        background-color: var(--midnight-steel);
        color: var(--golden-bloom-light);
        border-color: var(--golden-bloom);
        box-shadow: 0 0 0 0.25rem rgba(242, 197, 114, 0.25);
    }

    /* Botones principales */
    .btn-primary {
        background-color: var(--golden-bloom);
        border-color: var(--golden-bloom);
        color: var(--midnight-abyss);
        font-weight: bold;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(242, 197, 114, 0.3);
    }

    .btn-primary:hover {
        background-color: var(--golden-bloom-light);
        border-color: var(--golden-bloom-light);
        color: var(--midnight-abyss);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(242, 197, 114, 0.5);
    }

    /* Botón de eliminar */
    .btn-danger {
        background-color: #dc3545; /* Rojo de Bootstrap para acciones peligrosas */
        border-color: #dc3545;
        color: white;
        font-weight: bold;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
    }

    .btn-danger:hover {
        background-color: #c82333;
        border-color: #bd2130;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(220, 53, 69, 0.5);
    }

    /* Fondos personalizados para las secciones */
    .profile-picture-section {
        background: var(--midnight-abyss-light);
        border: 2px solid var(--golden-bloom);
    }
    
    .delete-account-section {
        background: var(--midnight-abyss-light);
        border: 2px solid #dc3545; /* Mantener borde rojo para enfatizar la acción */
    }

    /* Carrusel personalizado */
    .profile-carousel-custom {
        background: var(--midnight-steel);
        border-radius: 15px;
        padding: 20px;
        box-shadow: inset 0 0 15px rgba(0, 0, 0, 0.5);
        position: relative;
    }

    .carousel-inner {
        padding-bottom: 40px; /* Espacio para los indicadores */
    }

    /* Ocultar flechas de Bootstrap */
    .carousel-control-prev,
    .carousel-control-next {
        display: none !important;
    }

    /* Indicadores como botones de navegación */
    .carousel-indicators-custom {
        position: absolute;
        bottom: 10px;
        left: 0;
        right: 0;
        margin: 0;
        justify-content: center;
        display: flex;
        z-index: 15;
    }

    .carousel-indicators-custom button {
        width: 12px; /* Tamaño del círculo */
        height: 12px; /* Tamaño del círculo */
        border-radius: 50%;
        background-color: var(--midnight-steel-light); /* Color del círculo inactivo */
        border: 2px solid var(--golden-bloom); /* Borde dorado */
        margin: 0 6px; /* Espaciado entre círculos */
        transition: all 0.3s ease;
        opacity: 0.7;
        cursor: pointer; /* Indicar que son clickeables */
    }

    .carousel-indicators-custom button.active {
        background-color: var(--golden-bloom); /* Color del círculo activo */
        transform: scale(1.2); /* Efecto de crecimiento al estar activo */
        opacity: 1;
    }

    .carousel-indicators-custom button:hover:not(.active) {
        background-color: var(--golden-bloom-light); /* Color al pasar el ratón */
        transform: scale(1.1);
        opacity: 0.9;
    }

    /* Estilos para las imágenes de perfil */
    .profile-picture-selector {
        width: 80px;
        height: 80px;
        object-fit: cover;
        cursor: pointer;
        border: 3px solid transparent;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }

    .profile-picture-selector:hover {
        transform: scale(1.15);
        border-color: var(--golden-bloom) !important;
    }

    input[type="radio"]:checked + .profile-picture-selector {
        border-color: var(--golden-bloom) !important;
        transform: scale(1.1);
    }

    .current-profile-img {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border: 4px solid var(--golden-bloom);
        transition: all 0.3s ease;
    }

    .current-profile-img:hover {
        transform: scale(1.05) rotate(5deg);
    }

    .profile-picture-option {
        display: inline-block;
        margin: 5px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .profile-picture-selector {
            width: 60px !important;
            height: 60px !important;
        }
        
        .current-profile-img {
            width: 100px !important;
            height: 100px !important;
        }
        
        .profile-carousel-custom {
            padding: 15px;
        }

        .carousel-inner {
            padding-bottom: 30px;
        }

        .carousel-indicators-custom {
            bottom: 5px;
        }
    }

    /* Animaciones */
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }

    .profile-picture-selector.selected {
        animation: pulse 1s ease-in-out;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const currentProfilePicture = document.getElementById('currentProfilePicture');

        // Hacer que las imágenes sean clickeables
        document.querySelectorAll('.profile-picture-selector').forEach(function(img) {
            img.addEventListener('click', function() {
                const radio = this.previousElementSibling;
                radio.checked = true;
                
                // Actualizar la imagen actual inmediatamente para preview
                const pictureId = this.getAttribute('data-picture-id');
                currentProfilePicture.src = `{{ asset('images/profile') }}/${pictureId}.png`;
                
                // Remover selección anterior y agregar a la nueva
                document.querySelectorAll('.profile-picture-selector').forEach(function(otherImg) {
                    otherImg.style.borderColor = 'transparent';
                    otherImg.style.transform = 'scale(1)';
                    otherImg.classList.remove('selected');
                });
                
                this.style.borderColor = 'var(--golden-bloom)';
                this.style.transform = 'scale(1.1)';
                this.classList.add('selected');
            });
        });

        // Inicializar la selección actual
        const checkedRadio = document.querySelector('input[name="profile_picture"]:checked');
        if (checkedRadio) {
            const selectedImg = checkedRadio.nextElementSibling;
            selectedImg.style.borderColor = 'var(--golden-bloom)';
            selectedImg.style.transform = 'scale(1.1)';
            selectedImg.classList.add('selected');
        }

        // Efecto de hover mejorado para la imagen actual
        currentProfilePicture.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.05) rotate(5deg)';
        });

        currentProfilePicture.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1) rotate(0deg)';
        });
    });
</script>
@endsection