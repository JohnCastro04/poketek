@extends('layouts.app')

@section('title', ucfirst($pokemon['display_name']) . ' - Pokédex')

@push('styles')
<style>
    .nickname-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.5rem 0.75rem;
        margin-bottom: 0.5rem;
        border-radius: 0.5rem;
    }

    .nickname-text {
        font-weight: 500;
    }

    .delete-nickname-btn {
        background-color: transparent;
        border: none;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        cursor: pointer;
    }

    .add-nickname-btn {
        background-color: var(--golden-bloom);
        color: var(--midnight-abyss);
        font-weight: 600;
        border: none;
    }

    @keyframes loadStat {
        from {
            transform: scaleX(0);
            opacity: 0.5;
        }

        to {
            transform: scaleX(1);
            opacity: 1;
        }
    }

    @keyframes pokemon-pop {
        0% {
            transform: scale(0.8);
            opacity: 0;
        }

        50% {
            transform: scale(1.05);
            opacity: 1;
        }

        100% {
            transform: scale(1);
        }
    }

    #addNicknameModal .modal-content,
    #deleteNicknameModal .modal-content {
        background-color: var(--midnight-abyss);
        color: #E0E0E0;
        border: 1px solid var(--golden-bloom);
        border-radius: 0.75rem;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
    }

    #addNicknameModal .modal-header,
    #deleteNicknameModal .modal-header {
        border-bottom: 1px solid var(--golden-bloom);
        padding: 1rem 1.5rem;
    }

    #addNicknameModal .modal-header .modal-title,
    #deleteNicknameModal .modal-header .modal-title {
        color: var(--golden-bloom-light);
        font-weight: 600;
    }

    #addNicknameModal .modal-body,
    #deleteNicknameModal .modal-body {
        padding: 1.5rem;
    }

    #addNicknameModal .modal-footer,
    #deleteNicknameModal .modal-footer {
        border-top: 1px solid var(--golden-bloom);
        padding: 1rem 1.5rem;
        background-color: var(--midnight-abyss-light);
        border-bottom-left-radius: 0.75rem;
        border-bottom-right-radius: 0.75rem;
    }

    #addNicknameModal .form-control {
        background-color: var(--midnight-abyss-light);
        color: #FFFFFF;
        border: 1px solid #4F5D75;
        padding: 0.75rem 1rem;
    }

    #addNicknameModal .form-control:focus {
        background-color: var(--midnight-abyss-light);
        color: #FFFFFF;
        border-color: var(--golden-bloom-light);
        box-shadow: 0 0 0 0.25rem rgba(242, 197, 114, 0.25);
    }

    #addNicknameModal .form-label {
        color: var(--golden-bloom-light);
        font-weight: 500;
    }

    #addNicknameModal .form-text {
        color: #A0A0A0;
    }

    .modal-footer .btn {
        border: none;
        font-weight: 600;
        padding: 0.5rem 1.25rem;
        border-radius: 0.5rem;
    }

    #addNicknameModal .btn-primary {
        background-color: var(--golden-bloom);
        color: var(--midnight-abyss);
    }

    .modal-footer .btn-secondary {
        background-color: var(--midnight-abyss-light);
        color: #E0E0E0;
        border: 1px solid #4F5D75;
    }

    #deleteNicknameModal .btn-danger {
        background-color: #B71C1C;
        color: #FFFFFF;
    }

    .ability-btn {
        background-color: var(--midnight-abyss-light);
        color: var(--ice-blue-accent);
        border: 1px solid rgba(var(--space-cadet-light-rgb), 0.5);
        border-radius: 0.5rem;
        font-weight: 500;
        position: relative;
        overflow: hidden;
    }

    .ability-btn.active {
        background-color: var(--golden-bloom);
        color: var(--midnight-abyss);
        border-color: var(--golden-bloom-light);
        font-weight: 600;
    }

    .ability-btn .badge {
        background-color: rgba(var(--space-cadet-light-rgb), 0.8);
        color: var(--golden-bloom-light);
        font-size: 0.7rem;
        padding: 0.2rem 0.4rem;
        border-radius: 0.25rem;
    }

    .ability-btn.active .badge {
        background-color: var(--midnight-abyss);
        color: var(--golden-bloom);
    }

    .ability-btn.active::after {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
        animation: shine 1.5s infinite;
    }

    @keyframes shine {
        0% {
            transform: translateX(-100%);
        }

        100% {
            transform: translateX(100%);
        }
    }
</style>
@endpush


@section('content')
<div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content"
            style="background-color: var(--midnight-abyss); color: var(--ice-blue-accent); border: 1px solid var(--golden-bloom);">
            <div class="modal-header" style="border-bottom: 1px solid var(--space-cadet-medium);">
                <h5 class="modal-title" id="messageModalLabel"></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body" id="messageModalBody"></div>
            <div class="modal-footer" style="border-top: 1px solid var(--space-cadet-medium);">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="container pokemon-detail-container">
    <div class="header-section text-center py-4 px-3">
        <h1 class="display-4 fw-bold">
            {{ ucfirst($pokemon['display_name']) }}
            <span class="pokemon-id-badge">#{{ str_pad($pokemon['id'], 4, '0', STR_PAD_LEFT) }}</span>
        </h1>
        <div class="d-flex justify-content-center mt-3 flex-wrap">
            @php
            $typeTranslations = [
            'normal' => 'Normal',
            'fire' => 'Fuego',
            'water' => 'Agua',
            'electric' => 'Eléctrico',
            'grass' => 'Planta',
            'ice' => 'Hielo',
            'fighting' => 'Lucha',
            'poison' => 'Veneno',
            'ground' => 'Tierra',
            'flying' => 'Volador',
            'psychic' => 'Psíquico',
            'bug' => 'Bicho',
            'rock' => 'Roca',
            'ghost' => 'Fantasma',
            'dragon' => 'Dragón',
            'dark' => 'Siniestro',
            'steel' => 'Acero',
            'fairy' => 'Hada',
            ];
            @endphp
            @foreach ($pokemon['types'] as $type)
            @php
            $typeName = is_array($type) ? ($type['name'] ?? null) : $type;
            $translatedType = $typeTranslations[strtolower($typeName)] ?? ucfirst(str_replace('-', ' ', $typeName));
            @endphp
            @if($typeName)
            <span class="type-badge {{ strtolower($typeName) }} mx-1 my-1">{{ $translatedType }}</span>
            @endif
            @endforeach
        </div>
    </div>

    <div class="row g-4 bento-mosaic align-items-stretch">
        <div class="col-12 col-lg-5 d-flex flex-column gap-4 align-items-center">
            <div style="width:100%;max-width:340px;">
                <div
                    class="bento-card pokemon-img-container bento-img-square flex-grow-0 d-flex align-items-center justify-content-center mb-0">
                    <img src="{{ $pokemon['image'] }}" class="pokemon-image img-fluid mx-auto d-block"
                        alt="{{ $pokemon['display_name'] }}" loading="lazy">
                </div>

                <div
                    class="bento-card egg-group-card flex-grow-0 d-flex flex-column justify-content-center text-center h-auto mt-4">
                    <div class="card-body p-3 d-flex flex-column align-items-center justify-content-center">
                        <h5 class="card-title fw-bold mb-2">Grupo huevo</h5>
                        <div class="mt-2 d-flex flex-wrap gap-1 justify-content-center">
                            @php
                            $eggGroupTranslations = [
                            'monster' => 'Monstruo',
                            'water1' => 'Agua 1',
                            'bug' => 'Bicho',
                            'flying' => 'Volador',
                            'amorphous' => 'Amorfo',
                            'field' => 'Campo',
                            'fairy' => 'Hada',
                            'plant' => 'Planta',
                            'humanshape' => 'Humanoide',
                            'water3' => 'Agua 3',
                            'mineral' => 'Mineral',
                            'indeterminate' => 'Amorfo',
                            'undiscovered' => 'Desconocido',
                            'water2' => 'Agua 2',
                            'ditto' => 'Ditto',
                            'dragon' => 'Dragón',
                            'no-eggs' => 'Sin huevo',
                            'human-like' => 'Humanoide',
                            ];
                            @endphp
                            @forelse ($pokemon['egg_groups'] as $group)
                            @php
                            $groupName = is_array($group) ? ($group['name'] ?? null) : $group;
                            $key = str_replace(['_', ' '], '-', strtolower($groupName ?? ''));
                            $translated = $eggGroupTranslations[$key] ?? ucfirst(str_replace('-', ' ', $groupName ?? ''));
                            @endphp
                            @if ($groupName)
                            <span class="badge bg-secondary me-2 mb-1">{{ $translated }}</span>
                            @endif
                            @empty
                            <span class="text-muted small">No disponible</span>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div
                    class="bento-card nicknames-card flex-grow-0 d-flex flex-column justify-content-center text-center mt-4">
                    <div class="card-body p-3 d-flex flex-column align-items-center justify-content-center">
                        <div class="d-flex justify-content-between align-items-center w-100 mb-2">
                            <h5 class="card-title fw-bold mb-0">Motes</h5>
                            @auth
                            <button id="addNicknameBtn" type="button" class="btn btn-sm add-nickname-btn">
                                <i class="bi bi-plus-circle"></i> Añadir
                            </button>
                            @endauth
                        </div>
                        <div class="mt-2 text-light small w-100">
                            @auth
                            @if ($userNicknames->isNotEmpty())
                            <ul class="list-unstyled mb-0 text-start" id="nicknamesList">
                                @foreach ($userNicknames as $nickname)
                                <li class="nickname-item" data-id="{{ $nickname->id }}">
                                    <span class="nickname-text">{{ $nickname->name }}</span>
                                    <a type="button" class="delete-nickname-btn" data-id="{{ $nickname->id }}"
                                        title="Borrar mote">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                            @else
                            <p class="text-light">No has añadido motes para este Pokémon.</p>
                            @endif
                            @else
                            <p class="text-light">Inicia sesión para añadir y ver tus motes.</p>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-7 d-flex flex-column gap-4">
            <div class="row g-4">
                <div class="col-12">
                    <div class="bento-card stats-card flex-grow-0 d-flex flex-column h-100">
                        <div class="card-body p-3 d-flex flex-column">
                            <h5 class="card-title fw-bold mb-4">Estadísticas</h5>
                            <div class="row">
                                @php
                                $statTranslations = [
                                'hp' => 'PS',
                                'attack' => 'Ataque',
                                'defense' => 'Defensa',
                                'special-attack' => 'Atq. Especial',
                                'special-defense' => 'Def. Especial',
                                'speed' => 'Velocidad',
                                ];
                                @endphp
                                @foreach ($pokemon['stats'] as $statName => $statValue)
                                @php
                                $translatedStat = $statTranslations[strtolower($statName)] ?? ucfirst(str_replace('-', ' ', $statName));
                                $percentage = ($statValue / 255) * 100;
                                @endphp
                                <div class="col-12 stat-row mb-3">
                                    <div class="stat-name d-flex justify-content-between">
                                        <span>{{ $translatedStat }}</span>
                                        <span class="stat-value">{{ $statValue }}</span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar stat-loading" role="progressbar"
                                            style="width: {{ $percentage }}%" aria-valuenow="{{ $statValue }}"
                                            aria-valuemin="0" aria-valuemax="255">
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="bento-card description-card flex-grow-0 d-flex flex-column h-auto">
                        <div class="card-body p-3 d-flex flex-column">
                            <h5 class="card-title fw-bold mb-2">Descripción</h5>
                            <div class="text-light small mt-auto">
                                {{ $pokemon['description'] ?? 'Sin descripción disponible en español.' }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="bento-card ability-card flex-grow-0 d-flex flex-column h-100">
                        <div class="card-body p-3 d-flex flex-column h-100">
                            <h5 class="card-title fw-bold mb-2">Habilidades</h5>
                            <div class="d-flex flex-row flex-wrap gap-2 mb-3">
                                @foreach ($pokemon['abilities'] as $i => $ability)
                                <button type="button" class="btn ability-btn px-3 py-1 d-flex align-items-center"
                                    data-index="{{ $i }}">
                                    {{ $ability['name_es'] ?? $ability['name'] }}
                                    @if ($ability['is_hidden'] ?? false)
                                    <span class="badge ms-2">Oculta</span>
                                    @endif
                                </button>
                                @endforeach
                            </div>
                            <div id="ability-description" class="text-light small mt-auto">
                                @if(!empty($pokemon['abilities'][0]['description']))
                                <span>{{ $pokemon['abilities'][0]['description'] }}</span>
                                @else
                                <span class="text-muted">No hay descripción disponible en español.</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@auth
<div class="modal fade" id="addNicknameModal" tabindex="-1" aria-labelledby="addNicknameModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="addNicknameForm" action="{{ route('nickname.store', ['pokemon' => $pokemon['id']]) }}"
                method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addNicknameModalLabel">Añadir Mote a
                        {{ ucfirst($pokemon['display_name'] ?? 'este Pokémon') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nicknameName" class="form-label">Mote:</label>
                        <input type="text" class="form-control" id="nicknameName" name="name" required maxlength="20">
                        <div class="form-text mt-1">Máximo 20 caracteres.</div>
                        <div class="form-error-message text-danger small mt-2" style="display: none;"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Mote</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteNicknameModal" tabindex="-1" aria-labelledby="deleteNicknameModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteNicknameModalLabel">Confirmar eliminación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que quieres borrar este mote?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="deleteNicknameForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Borrar</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endauth
@endsection

@push('scripts')
<script>
    const abilityData = @json($pokemon['abilities']); 
    document.addEventListener('DOMContentLoaded', function() {
        // Inicialización de modales
        const messageModal = new bootstrap.Modal(document.getElementById('messageModal'));

        // Animación de barras de estadísticas
        const statBars = document.querySelectorAll('.stat-loading');
        statBars.forEach((bar, index) => {
            bar.style.transform = 'scaleX(0)';
            bar.style.opacity = '0.5';
            setTimeout(() => {
                bar.style.animation = `loadStat 0.8s cubic-bezier(0.22, 0.61, 0.36, 1) ${index * 0.1}s forwards`;
            }, 100);
        });

        // Toggle entre imagen shiny/normal
        const pokeImg = document.querySelector('.pokemon-image');
        if (pokeImg) {
            let isShiny = pokeImg.src.includes('/shiny/');
            const normalUrl = isShiny ? pokeImg.src.replace('/shiny/', '/official-artwork/') : pokeImg.src;
            const shinyUrl = isShiny ? pokeImg.src : pokeImg.src.replace('/official-artwork/', '/official-artwork/shiny/');

            pokeImg.style.cursor = 'pointer';
            pokeImg.title = isShiny ? 'Haz clic para ver la versión normal' : 'Haz clic para ver la versión shiny';

            pokeImg.addEventListener('click', function() {
                isShiny = !isShiny;
                pokeImg.style.transition = 'opacity 0.25s';
                pokeImg.style.opacity = '0';
                setTimeout(() => {
                    pokeImg.src = isShiny ? shinyUrl : normalUrl;
                    pokeImg.title = isShiny ? 'Haz clic para ver la versión normal' : 'Haz clic para ver la versión shiny';
                    pokeImg.style.opacity = '1';
                }, 220);
            });
        }

        // Mostrar descripción de habilidades
        const abilityBtns = document.querySelectorAll('.ability-btn');
        const abilityDesc = document.getElementById('ability-description');

        abilityBtns.forEach((btn, idx) => {
            btn.addEventListener('click', () => {
                const selectedAbility = abilityData[idx];
                abilityDesc.innerHTML = selectedAbility?.description ?
                    `<span>${selectedAbility.description}</span>` :
                    `<span class="text-muted">No hay descripción disponible en español.</span>`;

                abilityBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
            });
        });

        // Seleccionar primera habilidad al cargar
        if (abilityBtns.length > 0) abilityBtns[0].click();

        // Gestión de motes
        @auth
        const addNicknameModal = new bootstrap.Modal(document.getElementById('addNicknameModal'));
        const addNicknameForm = document.getElementById('addNicknameForm');

        // Abrir modal para añadir mote
        document.getElementById('addNicknameBtn')?.addEventListener('click', () => addNicknameModal.show());

        // Validación de formulario
        if (addNicknameForm) {
            addNicknameForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const nicknameName = document.getElementById('nicknameName').value.trim();
                const errorElement = addNicknameForm.querySelector('.form-error-message');

                if (!nicknameName) {
                    errorElement.textContent = 'El mote no puede estar vacío.';
                    errorElement.style.display = 'block';
                    return;
                }

                this.submit();
            });
        }

        // Confirmación para borrar mote
        document.querySelectorAll('.delete-nickname-btn').forEach(button => {
            button.addEventListener('click', function() {
                const deleteForm = document.getElementById('deleteNicknameForm');
                deleteForm.action = `/nickname/${this.dataset.id}`;
                new bootstrap.Modal(document.getElementById('deleteNicknameModal')).show();
            });
        });
        @endauth
    });
</script>
@endpush