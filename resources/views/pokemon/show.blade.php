{{-- filepath: c:\xampp\htdocs\poketek\resources\views\pokemon\show.blade.php --}}
@extends('layouts.app')

@section('title', ucfirst($pokemon['display_name']) . ' - Pokédex')

@push('styles')
    <style>
        /* Estilos para el botón de borrar mote */
        .nickname-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.5rem 0.75rem;
            margin-bottom: 0.5rem;
            background-color: rgba(var(--space-cadet-light-rgb), 0.3);
            border-radius: 0.5rem;
            transition: background-color 0.2s;
        }

        .nickname-item:hover {
            background-color: rgba(var(--space-cadet-light-rgb), 0.5);
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
            transition: all 0.2s;
        }

        .add-nickname-btn {
            background-color: var(--golden-bloom);
            color: var(--midnight-abyss);
            font-weight: 600;
            border: none;
            transition: all 0.2s;
        }

        .add-nickname-btn:hover {
            background-color: var(--golden-bloom-darken);
            transform: translateY(-1px);
        }

        /* Keyframe animations */
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
    </style>
@endpush

@section('content')
    <div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background-color: var(--midnight-abyss); color: var(--ice-blue-accent); border: 1px solid var(--golden-bloom);">
                <div class="modal-header" style="border-bottom: 1px solid var(--space-cadet-medium);">
                    <h5 class="modal-title" id="messageModalLabel"></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="messageModalBody">
                </div>
                <div class="modal-footer" style="border-top: 1px solid var(--space-cadet-medium);">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                        style="background-color: var(--midnight-steel); border-color: var(--golden-bloom); color: var(--golden-bloom);">Cerrar</button>
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
                        // Assuming $type is either a string like 'fire' or an associative array with 'name' key
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
                    <div class="bento-card pokemon-img-container bento-img-square flex-grow-0 d-flex align-items-center justify-content-center mb-0"
                        style="min-height:320px;max-height:380px;">
                        <img src="{{ $pokemon['image'] }}" class="pokemon-image img-fluid mx-auto d-block"
                            alt="{{ $pokemon['display_name'] }}" loading="lazy" style="max-width: 100%; max-height: 340px;">
                    </div>
                    <div
                        class="bento-card egg-group-card flex-grow-0 d-flex flex-column justify-content-center text-center h-auto mt-4">
                        <div class="card-body p-3 d-flex flex-column align-items-center justify-content-center">
                            <h5 class="card-title fw-bold mb-2" style="color: var(--golden-bloom);">Grupo huevo</h5>
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
                                <h5 class="card-title fw-bold mb-0" style="color: var(--golden-bloom);">Motes</h5>
                                @auth
                                    <button id="addNicknameBtn" type="button" class="btn btn-sm add-nickname-btn">
                                        <i class="fas fa-plus"></i> Añadir
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
                                                    <button type="button" class="delete-nickname-btn" data-id="{{ $nickname->id }}"
                                                        title="Borrar mote">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </li>
                                            @endforeach
                                        </ul>
                                        <p class="text-light mt-2" id="noNicknamesMsg" style="display: none;">No has añadido motes
                                            para este Pokémon.</p>
                                    @else
                                        <p class="text-light" id="noNicknamesMsg">No has añadido motes para este Pokémon.</p>
                                    @endif
                                @else
                                    <p class="text-light">Inicia sesión para añadir y ver tus motes.</p>
                                    <a href="{{ route('login') }}" class="btn btn-sm add-nickname-btn">
                                        Iniciar Sesión
                                    </a>
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
                                <h5 class="card-title fw-bold mb-4" style="color: var(--golden-bloom);">Estadísticas</h5>
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
                                            $percentage = ($statValue / 255) * 100; // Max stat is 255
                                        @endphp
                                        <div class="col-12 stat-row mb-3">
                                            <div class="stat-name d-flex justify-content-between">
                                                <span>{{ $translatedStat }}</span>
                                                <span class="stat-value">{{ $statValue }}</span>
                                            </div>
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar stat-loading" role="progressbar"
                                                    style="width: {{ $percentage }}%" aria-valuenow="{{ $statValue }}"
                                                    aria-valuemin="0" aria-valuemax="255" data-percentage="{{ $percentage }}">
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
                                <h5 class="card-title fw-bold mb-2" style="color: var(--golden-bloom);">Descripción</h5>
                                <div id="pokemon-description" class="text-light small mt-auto" style="min-height:2.5em;">
                                    <span>
                                        {{ $pokemon['description'] ?? 'Sin descripción disponible en español.' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="bento-card ability-card flex-grow-0 d-flex flex-column h-100">
                            <div class="card-body p-3 d-flex flex-column h-100">
                                <h5 class="card-title fw-bold mb-2" style="color: var(--golden-bloom);">Habilidades</h5>
                                <div id="abilities-list" class="d-flex flex-row flex-wrap gap-2 mb-3">
                                    @foreach ($pokemon['abilities'] as $i => $ability)
                                                                @php
                                                                    $isHidden = $ability['is_hidden'] ?? false;
                                                                    $abilityName = $ability['name_es'] ?? ucfirst(str_replace('-', ' ', $ability['name'] ?? 'Desconocida'));
                                                                @endphp
                                                                <button type="button" class="btn ability-btn px-3 py-1 d-flex align-items-center" style="
                                                                                {{ $isHidden
                                        ? 'background: var(--midnight-steel); color: var(--golden-bloom);'
                                        : 'background: var(--golden-bloom); color: var(--midnight-abyss);'
                                                                                }}
                                                                                font-weight:700; border: none;" data-index="{{ $i }}">
                                                                    {{ $abilityName }}
                                                                    @if ($isHidden)
                                                                        <span class="badge ms-2"
                                                                            style="background: var(--golden-bloom); color: var(--midnight-abyss); font-size: 0.75em; font-weight: 700; border-radius: 8px; letter-spacing: 0.5px;">
                                                                            Oculta
                                                                        </span>
                                                                    @endif
                                                                </button>
                                    @endforeach
                                </div>
                                <div id="ability-description" class="text-light small mt-auto" style="min-height:2.5em;">
                                    <span class="text-muted">Selecciona una habilidad para ver la descripción.</span>
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
                <div class="modal-content"
                    style="background-color: var(--midnight-abyss); color: var(--ice-blue-accent); border: 1px solid var(--golden-bloom);">
                    <form id="addNicknameForm" action="{{ route('nickname.store', ['pokemon' => $pokemon['id']]) }}"
                        method="POST">
                        @csrf
                        <div class="modal-header" style="border-bottom: 1px solid var(--space-cadet-medium);">
                            <h5 class="modal-title" id="addNicknameModalLabel" style="color: var(--golden-bloom);">Añadir Mote a
                                {{ ucfirst($pokemon['display_name'] ?? 'este Pokémon') }}</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="nicknameName" class="form-label">Mote:</label>
                                <input type="text" class="form-control" id="nicknameName" name="name" required maxlength="50"
                                    style="background-color: var(--space-cadet-light); color: var(--ice-blue-accent); border-color: var(--golden-bloom);">
                                <div class="form-text mt-1" style="color: var(--ice-blue-muted);">Máximo 20 caracteres.</div>
                                <div class="form-error-message text-danger small mt-2" style="display: none;"></div>
                            </div>
                        </div>
                        <div class="modal-footer" style="border-top: 1px solid var(--space-cadet-medium);">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                                style="background-color: var(--midnight-steel); border-color: var(--golden-bloom); color: var(--golden-bloom);">Cancelar</button>
                            <button type="submit" class="btn"
                                style="background-color: var(--golden-bloom); color: var(--midnight-abyss); font-weight: 700;">Guardar
                                Mote</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Delete Confirmation Modal --}}
        <div class="modal fade" id="deleteNicknameModal" tabindex="-1" aria-labelledby="deleteNicknameModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="background-color: var(--midnight-abyss); color: var(--ice-blue-accent); border: 1px solid var(--golden-bloom);">
                    <div class="modal-header" style="border-bottom: 1px solid var(--space-cadet-medium);">
                        <h5 class="modal-title" id="deleteNicknameModalLabel" style="color: var(--golden-bloom);">Confirmar eliminación</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>¿Estás seguro de que quieres borrar este mote?</p>
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid var(--space-cadet-medium);">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            style="background-color: var(--midnight-steel); border-color: var(--golden-bloom); color: var(--golden-bloom);">Cancelar</button>
                        <form id="deleteNicknameForm" method="POST">
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
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize modals
            const messageModal = new bootstrap.Modal(document.getElementById('messageModal'));
            const deleteNicknameModal = document.getElementById('deleteNicknameModal') ? 
                new bootstrap.Modal(document.getElementById('deleteNicknameModal')) : null;
            
            // Function to show message modal
            function showMessage(title, message, type = 'success') {
                const modal = document.getElementById('messageModal');
                const modalTitle = modal.querySelector('.modal-title');
                const modalBody = modal.querySelector('.modal-body');
                
                modalTitle.textContent = title;
                modalBody.innerHTML = `<p>${message}</p>`;
                
                // Set color based on type
                const header = modal.querySelector('.modal-header');
                if (type === 'success') {
                    header.style.borderBottomColor = 'var(--success)';
                } else if (type === 'error') {
                    header.style.borderBottomColor = 'var(--danger)';
                }
                
                messageModal.show();
            }

            // Check for flash messages and show them in the modal
            @if(session('status'))
                showMessage('Éxito', '{{ session('status') }}', 'success');
            @endif
            
            @if(session('error'))
                showMessage('Error', '{{ session('error') }}', 'error');
            @endif

            // --- Stat Bars Animation ---
            const animateStatBars = () => {
                const statBars = document.querySelectorAll('.stat-loading');
                statBars.forEach((bar, index) => {
                    bar.style.transform = 'scaleX(0)';
                    bar.style.opacity = '0.5';
                    setTimeout(() => {
                        bar.style.animation = `loadStat 0.8s cubic-bezier(0.22, 0.61, 0.36, 1) ${index * 0.1}s forwards`;
                    }, 100);
                });
            };
            animateStatBars(); // Run on page load

            // --- Pokemon Image Shiny Toggle ---
            const pokeImg = document.querySelector('.pokemon-image');
            if (pokeImg) {
                let isShiny = pokeImg.src.includes('/shiny/'); // Check if initial image is shiny
                const initialSrc = pokeImg.src;
                const normalUrl = isShiny ? initialSrc.replace('/shiny/', '/official-artwork/') : initialSrc;
                const shinyUrl = isShiny ? initialSrc : initialSrc.replace('/official-artwork/', '/official-artwork/shiny/');

                pokeImg.style.cursor = 'pointer';
                pokeImg.title = isShiny ? 'Haz clic para ver la versión normal' : 'Haz clic para ver la versión shiny';

                pokeImg.addEventListener('click', function () {
                    isShiny = !isShiny;
                    pokeImg.style.transition = 'opacity 0.25s';
                    pokeImg.style.opacity = '0';
                    setTimeout(() => {
                        pokeImg.src = isShiny ? shinyUrl : normalUrl;
                        pokeImg.title = isShiny ? 'Haz clic para ver la versión normal' : 'Haz clic para ver la versión shiny';
                        pokeImg.style.opacity = '1'; // Instantly set opacity back to 1 after src change for smooth transition
                    }, 220); // This timeout should match or be slightly less than the opacity transition
                });

                pokeImg.addEventListener('load', function () {
                    // This listener ensures the pop animation plays *after* the image loads
                    pokeImg.style.animation = 'pokemon-pop 0.6s cubic-bezier(0.22, 0.61, 0.36, 1)';
                    setTimeout(() => {
                        pokeImg.style.animation = ''; // Clear animation after it finishes
                    }, 600);
                });
            }

            // --- Ability Description Toggle ---
            const abilityBtns = document.querySelectorAll('.ability-btn');
            const abilityDesc = document.getElementById('ability-description');
            // Assuming $pokemon['abilities'] is always available and structured as expected from the controller
            const abilityData = @json($pokemon['abilities']);

            const selectAbility = (idx) => {
                const selectedAbility = abilityData[idx];
                let descHTML = `<span class="text-muted">No hay descripción disponible en español.</span>`;

                if (selectedAbility && selectedAbility.description) {
                    descHTML = `<span>${selectedAbility.description}</span>`;
                }

                abilityDesc.innerHTML = descHTML;

                abilityBtns.forEach(b => b.classList.remove('active'));
                if (abilityBtns[idx]) {
                    abilityBtns[idx].classList.add('active');
                }
            };

            abilityBtns.forEach((btn, idx) => {
                btn.addEventListener('click', () => selectAbility(idx));
            });

            // Select the first ability on load if available
            if (abilityBtns.length > 0) {
                selectAbility(0);
            }

            // --- Nickname Management (Add/Delete) ---
            const addNicknameModalElement = document.getElementById('addNicknameModal');
            const addNicknameModal = addNicknameModalElement ? new bootstrap.Modal(addNicknameModalElement) : null;
            const addNicknameForm = document.getElementById('addNicknameForm');
            const addNicknameBtn = document.getElementById('addNicknameBtn');

            // Event listener for opening the add nickname modal
            if (addNicknameBtn && addNicknameModal) {
                addNicknameBtn.addEventListener('click', () => addNicknameModal.show());
            }

            // Handle add nickname form submission
            if (addNicknameForm) {
                addNicknameForm.addEventListener('submit', function (event) {
                    event.preventDefault();
                    const formData = new FormData(addNicknameForm);
                    const nicknameNameInput = document.getElementById('nicknameName');
                    const nicknameName = nicknameNameInput.value.trim();
                    const errorElement = addNicknameModalElement.querySelector('.form-error-message');

                    errorElement.textContent = '';
                    errorElement.style.display = 'none';
                    nicknameNameInput.classList.remove('is-invalid');

                    if (!nicknameName) {
                        errorElement.textContent = 'El mote no puede estar vacío.';
                        errorElement.style.display = 'block';
                        nicknameNameInput.classList.add('is-invalid');
                        return;
                    }

                    // Submit the form normally (will cause page reload)
                    addNicknameForm.submit();
                });
            }

            // Handle nickname deletion via modal confirmation
            document.querySelectorAll('.delete-nickname-btn').forEach(button => {
                button.addEventListener('click', function (event) {
                    event.preventDefault();
                    const nicknameId = this.dataset.id;
                    
                    // Set up the delete form
                    const deleteForm = document.getElementById('deleteNicknameForm');
                    deleteForm.action = `/nickname/${nicknameId}`;
                    
                    // Show the confirmation modal
                    if (deleteNicknameModal) {
                        deleteNicknameModal.show();
                    }
                });
            });

            // Reset form on modal hide
            if (addNicknameModalElement) {
                addNicknameModalElement.addEventListener('hidden.bs.modal', function () {
                    const form = document.getElementById('addNicknameForm');
                    if (form) {
                        form.reset();
                        const errorElement = addNicknameModalElement.querySelector('.form-error-message');
                        errorElement.style.display = 'none';
                        const nicknameNameInput = document.getElementById('nicknameName');
                        nicknameNameInput.classList.remove('is-invalid');
                    }
                });
            }
        });
    </script>
@endpush