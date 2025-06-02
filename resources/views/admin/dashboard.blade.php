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

                    {{-- Filtros --}}
                    <div class="row row-cols-1 row-cols-md-2 g-3 mb-4">
                        <div class="col">
                            <input type="text" id="userSearch" class="form-control"
                                placeholder="Buscar por nombre o email..."
                                style="background-color: var(--midnight-steel-light); color: var(--white); border: 1px solid var(--midnight-steel); border-radius: 8px; font-family: var(--font-text);">
                        </div>
                        <div class="col">
                            <select id="permissionFilter" class="form-select"
                                style="background-color: var(--midnight-steel-light); color: var(--white); border: 1px solid var(--midnight-steel); border-radius: 8px; padding: 10px; font-family: var(--font-text);">
                                <option value="all">Todos los permisos</option>
                                <option value="1">Administrador (1)</option>
                                <option value="0">Usuario Normal (0)</option>
                            </select>

                        </div>
                    </div>

                    {{-- Tabla --}}
                    <div class="table-responsive" style="border-radius: var(--card-border-radius); overflow: hidden;">
                        <table class="table table-hover mb-0"
                            style="color: var(--white); background-color: var(--midnight-abyss-light);">
                            <thead style="background-color: var(--midnight-steel);">
                                <tr>
                                    <th style="color: var(--golden-bloom); border-bottom: 2px solid var(--golden-bloom);">
                                        Nombre</th>
                                    <th style="color: var(--golden-bloom); border-bottom: 2px solid var(--golden-bloom);">
                                        Email</th>
                                    <th style="color: var(--golden-bloom); border-bottom: 2px solid var(--golden-bloom);">
                                        Permiso</th>
                                    <th class="text-end"
                                        style="color: var(--golden-bloom); border-bottom: 2px solid var(--golden-bloom);">
                                        Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="userTableBody">
                                @foreach ($users as $user)
                                    <tr class="user-row" data-permission="{{ $user->permission ? '1' : '0' }}"
                                        style="background-color: var(--midnight-abyss-light);">
                                        <td style="border-bottom: 1px solid var(--midnight-steel-light);">{{ $user->name }}</td>
                                        <td style="border-bottom: 1px solid var(--midnight-steel-light);">{{ $user->email }}
                                        </td>
                                        <td style="border-bottom: 1px solid var(--midnight-steel-light);">
                                            {{ $user->permission ? 'Admin (1)' : 'Usuario (0)' }}
                                        </td>
                                        <td class="text-end" style="border-bottom: 1px solid var(--midnight-steel-light);">
                                            <div class="d-flex flex-wrap justify-content-end gap-2">
                                                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm"
                                                    style="background-color: var(--golden-bloom); color: var(--midnight-abyss); font-weight: 700;"
                                                    onmouseover="this.style.backgroundColor='var(--golden-bloom-light)'; this.style.transform='scale(1.05)';"
                                                    onmouseout="this.style.backgroundColor='var(--golden-bloom)'; this.style.transform='scale(1)';">
                                                    Editar
                                                </a>
                                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                                                    class="d-inline"
                                                    onsubmit="return confirm('¿Estás seguro de que quieres eliminar a este usuario? Esta acción no se puede deshacer.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm"
                                                        style="background-color: #FF6347; color: var(--white); font-weight: 700;"
                                                        onmouseover="this.style.backgroundColor='#CD5C5C'; this.style.transform='scale(1.05)';"
                                                        onmouseout="this.style.backgroundColor='#FF6347'; this.style.transform='scale(1)';">
                                                        Eliminar
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

        /* Opcional: mejor ajuste en pantallas pequeñas */
        @media (max-width: 576px) {
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

            table thead {
                display: none;
            }

            table tbody tr {
                display: block;
                margin-bottom: 1rem;
                border: 1px solid var(--midnight-steel-light);
                border-radius: 8px;
                padding: 0.5rem;
            }

            table tbody td {
                display: flex;
                justify-content: space-between;
                padding: 0.4rem 0;
                border: none !important;
            }

            table tbody td::before {
                content: attr(data-label);
                font-weight: bold;
            }
        }
    </style>
@endpush


@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const userSearch = document.getElementById('userSearch');
            const permissionFilter = document.getElementById('permissionFilter');
            const userTableBody = document.getElementById('userTableBody');
            const userRows = userTableBody.querySelectorAll('.user-row');

            function filterUsers() {
                const searchTerm = userSearch.value.toLowerCase();
                const selectedPermission = permissionFilter.value;

                userRows.forEach(row => {
                    const name = row.cells[0].textContent.toLowerCase();
                    const email = row.cells[1].textContent.toLowerCase();
                    const permission = row.dataset.permission; // ya es string '1' o '0'

                    const matchesSearch = name.includes(searchTerm) || email.includes(searchTerm);
                    const matchesPermission = selectedPermission === 'all' || permission === selectedPermission;

                    row.style.display = (matchesSearch && matchesPermission) ? '' : 'none';
                });
            }

            userSearch.addEventListener('input', filterUsers);
            permissionFilter.addEventListener('change', filterUsers);

            // Hover effect
            userRows.forEach(row => {
                row.addEventListener('mouseenter', function () {
                    this.style.backgroundColor = 'var(--midnight-steel-light)';
                    this.style.transition = 'background-color var(--transition-time)';
                });
                row.addEventListener('mouseleave', function () {
                    this.style.backgroundColor = 'var(--midnight-abyss-light)';
                });
            });
        });
    </script>
@endpush