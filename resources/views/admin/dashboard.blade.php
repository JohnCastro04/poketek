@extends('layouts.app')

@section('title', config('app.name') . ' - Panel de Administración')

@section('content')
    <section class="header-section text-center py-4">
        <div class="container">
            <h1 class="display-4">Panel de Administración</h1>
            <p class="description-card">Gestión de usuarios del sistema.</p>
        </div>
    </section>

    <section id="user-management" class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="bento-card p-4">
                    <h3 class="card-title text-center mb-4">
                        Lista de Usuarios Registrados
                    </h3>

                    {{-- Mensajes de estado --}}
                    @if (session('status'))
                        <div class="alert alert-success text-center mb-4"
                            style="color: #90EE90; background-color: rgba(144, 238, 144, 0.1); border: 1px solid #90EE90;">
                            {{ session('status') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger text-center mb-4"
                            style="color: #FF6347; background-color: rgba(255, 99, 71, 0.1); border: 1px solid #FF6347;">
                            {{ session('error') }}
                        </div>
                    @endif

                    {{-- Opciones de búsqueda y filtro --}}
                    <div class="row row-cols-1 row-cols-md-2 g-3 mb-4">
                        <div class="col">
                            <input type="text" id="busquedaUsuario" class="form-control"
                                placeholder="Buscar por nombre o email..."
                                style="background-color: var(--midnight-steel-light); color: var(--white); border: 1px solid var(--midnight-steel); border-radius: 8px; font-family: var(--font-text);">
                        </div>
                        <div class="col">
                            <select id="filtroPermiso" class="form-select"
                                style="background-color: var(--midnight-steel-light); color: var(--white); border: 1px solid var(--midnight-steel); border-radius: 8px; padding: 10px; font-family: var(--font-text);">
                                <option value="all">Todos los permisos</option>
                                <option value="1">Administrador (1)</option>
                                <option value="0">Usuario Normal (0)</option>
                            </select>
                        </div>
                    </div>

                    {{-- Tabla de usuarios --}}
                    <div class="table-responsive" style="border-radius: var(--card-border-radius); overflow: hidden;">
                        <table class="table table-hover mb-0"
                            style="color: var(--white); background-color: var(--midnight-abyss-light);">
                            <thead style="background-color: var(--midnight-steel);">
                                <tr>
                                    <th class="text-center" style="color: var(--golden-bloom); border-bottom: 2px solid var(--golden-bloom);">
                                        Usuario</th>
                                    <th class="text-center" style="color: var(--golden-bloom); border-bottom: 2px solid var(--golden-bloom);">
                                        Email</th>
                                    <th class="text-center" style="color: var(--golden-bloom); border-bottom: 2px solid var(--golden-bloom);">
                                        Permiso</th>
                                    <th class="text-center"
                                        style="color: var(--golden-bloom); border-bottom: 2px solid var(--golden-bloom);">
                                        Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="cuerpoTablaUsuarios">
                                @foreach ($users as $user)
                                    <tr class="fila-usuario" data-permission="{{ $user->permission ? '1' : '0' }}"
                                        style="background-color: var(--midnight-abyss-light);">
                                        <td class="text-center" style="border-bottom: 1px solid var(--midnight-steel-light); vertical-align: middle; white-space: nowrap;">
                                            <img src="{{ $user->profile_picture_url }}" alt="Foto de {{ $user->name }}"
                                                class="miniatura-perfil-inline me-2"
                                                style="width: 30px; height: 30px; vertical-align: middle;">
                                            {{ $user->name }}
                                        </td>
                                        <td class="text-center" style="border-bottom: 1px solid var(--midnight-steel-light);">{{ $user->email }}
                                        </td>
                                        <td class="text-center" style="border-bottom: 1px solid var(--midnight-steel-light);">
                                            {{ $user->permission ? 'Admin (1)' : 'Usuario (0)' }}
                                        </td>
                                        <td class="text-center" style="border-bottom: 1px solid var(--midnight-steel-light);">
                                            <div class="d-flex flex-wrap justify-content-center gap-2">
                                                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm boton-icono-accion"
                                                    title="Editar usuario"
                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                    style="background-color: var(--golden-bloom); color: var(--midnight-abyss); font-weight: 700;"
                                                    onmouseover="this.style.backgroundColor='var(--golden-bloom-light)'; this.style.transform='scale(1.05)';"
                                                    onmouseout="this.style.backgroundColor='var(--golden-bloom)'; this.style.transform='scale(1)';">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                                                    class="d-inline"
                                                    onsubmit="return confirm('¿Estás seguro de que quieres eliminar a este usuario? Esta acción no se puede deshacer.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm boton-icono-accion"
                                                        title="Eliminar usuario"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        style="background-color: #FF6347; color: var(--white); font-weight: 700;"
                                                        onmouseover="this.style.backgroundColor='#CD5C5C'; this.style.transform='scale(1.05)';"
                                                        onmouseout="this.style.backgroundColor='#FF6347'; this.style.transform='scale(1)';">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        .table tbody tr,
        .table td,
        .table th {
            background-color: var(--midnight-steel) !important;
        }

        .table tbody td {
            color: var(--white) !important;
        }

        /* Centrado de texto en las celdas */
        .table th, .table td {
            text-align: center;
        }

        /* Estilo para la imagen de perfil inline */
        .miniatura-perfil-inline {
            border-radius: 50%;
            border: none;
            box-shadow: none;
        }

        /* Estilo para los botones de icono */
        .boton-icono-accion {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            border-radius: 8px;
            padding: 0;
        }

        .boton-icono-accion i {
            font-size: 1.2rem;
        }

        /* Estilo del tooltip */
        .tooltip-inner {
            background-color: var(--midnight-steel);
            color: var(--white);
            padding: 0.5rem 0.75rem;
            border-radius: 0.25rem;
            font-size: 0.875rem;
        }

        .tooltip.bs-tooltip-top .tooltip-arrow::before {
            border-top-color: var(--midnight-steel);
        }
        .tooltip.bs-tooltip-right .tooltip-arrow::before {
            border-right-color: var(--midnight-steel);
        }
        .tooltip.bs-tooltip-bottom .tooltip-arrow::before {
            border-bottom-color: var(--midnight-steel);
        }
        .tooltip.bs-tooltip-left .tooltip-arrow::before {
            border-left-color: var(--midnight-steel);
        }

        /* Responsive para la tabla en pantallas pequeñas */
        @media (max-width: 768px) {
            .header-section h1 {
                font-size: 1.75rem;
            }

            .description-card {
                font-size: 0.9rem;
            }

            .form-control,
            .form-select {
                font-size: 0.9rem;
            }

            .table thead {
                display: none;
            }

            .table tbody, .table tr {
                display: block;
                width: 100%;
            }

            .table tr {
                margin-bottom: 1rem;
                border: 1px solid var(--midnight-steel-light);
                border-radius: 8px;
                padding: 0.5rem;
                display: flex;
                flex-direction: column;
            }

            .table tbody td {
                display: flex;
                justify-content: space-between;
                padding: 0.4rem 0;
                border: none !important;
                text-align: left;
            }

            .table tbody td::before {
                content: attr(data-label);
                font-weight: bold;
                margin-right: 0.5rem;
                min-width: 80px;
            }

            /* Etiquetas para las celdas en móvil */
            .table tbody td:nth-child(1)::before { content: 'Usuario: '; }
            .table tbody td:nth-child(2)::before { content: 'Email: '; }
            .table tbody td:nth-child(3)::before { content: 'Permiso: '; }
            .table tbody td:nth-child(4)::before { content: 'Acciones: '; }
            
            /* Ajuste para la celda de acciones en móvil */
            .table tbody td:nth-child(4) {
                justify-content: flex-start;
            }
            .table tbody td:nth-child(4) .d-flex {
                width: 100%;
                justify-content: flex-start;
            }

            .miniatura-perfil-inline {
                margin-right: 0.5rem;
            }
        }

        .bi::before, [class*=" bi-"]::before, [class^=bi-]::before {
            vertical-align: -30% !important;
        }

        .row {
            align-items: center;
        }
    </style>
@endpush


@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const busquedaUsuario = document.getElementById('busquedaUsuario');
            const filtroPermiso = document.getElementById('filtroPermiso');
            const cuerpoTablaUsuarios = document.getElementById('cuerpoTablaUsuarios');
            const filasUsuario = cuerpoTablaUsuarios.querySelectorAll('.fila-usuario');

            function filtrarUsuarios() {
                const terminoBusqueda = busquedaUsuario.value.toLowerCase();
                const permisoSeleccionado = filtroPermiso.value;

                filasUsuario.forEach(fila => {
                    const nombre = fila.cells[0].textContent.toLowerCase();
                    const email = fila.cells[1].textContent.toLowerCase();
                    const permiso = fila.dataset.permission;

                    const coincideBusqueda = nombre.includes(terminoBusqueda) || email.includes(terminoBusqueda);
                    const coincidePermiso = permisoSeleccionado === 'all' || permiso === permisoSeleccionado;

                    fila.style.display = (coincideBusqueda && coincidePermiso) ? '' : 'none';
                });
            }

            busquedaUsuario.addEventListener('input', filtrarUsuarios);
            filtroPermiso.addEventListener('change', filtrarUsuarios);

            // Efecto hover en filas
            filasUsuario.forEach(fila => {
                fila.addEventListener('mouseenter', function () {
                    this.style.backgroundColor = 'var(--midnight-steel-light)';
                    this.style.transition = 'background-color var(--transition-time)';
                });
                fila.addEventListener('mouseleave', function () {
                    this.style.backgroundColor = 'var(--midnight-abyss-light)';
                });
            });

            // Inicializar Tooltips de Bootstrap
            var listaActivadoresTooltip = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var listaTooltips = listaActivadoresTooltip.map(function (elementoActivadorTooltip) {
                return new bootstrap.Tooltip(elementoActivadorTooltip)
            })
        });
    </script>
@endpush