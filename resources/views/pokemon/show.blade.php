<!DOCTYPE html>
<html lang="es">
<head>
    @vite(['resources/js/app.js'])
    <title>{{ ucfirst($pokemon['display_name']) }} - Pokédex</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    @include('layouts.header')
    <div class="container pokemon-detail-container"><!-- Cambiado py-5 a py-3 -->
        <!-- Header Section -->
        <div class="header-section text-center py-4 px-3np">
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
                        'fairy' => 'Hada'
                    ];
                @endphp
                @foreach($pokemon['types'] as $type)
                    @php
                        $typeName = is_array($type) ? $type[0] : $type;
                        $translatedType = $typeTranslations[strtolower($typeName)] ?? ucfirst($typeName);
                    @endphp
                    <span class="type-badge {{ strtolower($typeName) }} mx-1 my-1">{{ $translatedType }}</span>
                @endforeach
            </div>
        </div>

        <!-- Main Content: Responsive Bento Layout -->
        <div class="row g-4 align-items-stretch">
            <!-- Left Column: Imagen y Descripción -->
            <div class="col-lg-4 d-flex flex-column gap-4 h-100">
                <div class="bento-card pokemon-img-container mb-0 flex-grow-0 flex-shrink-0">
                    <img src="{{ $pokemon['image'] }}" class="pokemon-image img-fluid mx-auto d-block"
                         alt="{{ $pokemon['display_name'] }}" loading="lazy" style="max-height:220px;">
                </div>
                <div class="bento-card description-card flex-grow-1 d-flex flex-column">
                    <div class="card-body p-4 d-flex flex-column h-100">
                        <h5 class="card-title fw-bold mb-2" style="color: var(--golden-bloom);">Descripción</h5>
                        <p class="text-light small mt-auto" style="color: var(--white); flex:1;">
                            {{ $pokemon['description'] ?? 'Sin descripción.' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Right Column: Stats, Habilidades y Grupos Huevo -->
            <div class="col-lg-8 d-flex flex-column gap-4 h-100">
                <!-- Estadísticas -->
                <div class="bento-card stats-card flex-grow-1 d-flex flex-column">
                    <div class="card-body p-4 d-flex flex-column h-100">
                        <h5 class="card-title fw-bold mb-4" style="color: var(--golden-bloom);">Estadísticas</h5>
                        <div class="row">
                            @php
                                $statTranslations = [
                                    'hp' => 'PS',
                                    'attack' => 'Ataque',
                                    'defense' => 'Defensa',
                                    'special-attack' => 'Atq. Especial',
                                    'special-defense' => 'Def. Especial',
                                    'speed' => 'Velocidad'
                                ];
                            @endphp
                            @foreach($pokemon['stats'] as $statName => $statValue)
                                @php
                                    $translatedStat = $statTranslations[$statName] ?? ucfirst(str_replace('-', ' ', $statName));
                                    $percentage = ($statValue / 255) * 100;
                                @endphp
                                <div class="col-12 col-md-6 stat-row mb-3">
                                    <div class="stat-name d-flex justify-content-between">
                                        <span>{{ $translatedStat }}</span>
                                        <span class="stat-value">{{ $statValue }}</span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar stat-loading"
                                             role="progressbar"
                                             style="width: {{ $percentage }}%"
                                             aria-valuenow="{{ $statValue }}"
                                             aria-valuemin="0"
                                             aria-valuemax="255"
                                             data-percentage="{{ $percentage }}">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Habilidades y Grupos Huevo en una fila -->
                <div class="row g-4 flex-grow-0">
                    <!-- Habilidades -->
                    <div class="col-md-6">
                        <div class="bento-card ability-card h-100 d-flex flex-column">
                            <div class="card-body p-4 d-flex flex-column h-100">
                                <h5 class="card-title fw-bold mb-2" style="color: var(--golden-bloom);">Habilidades</h5>
                                <div id="abilities-list" class="d-flex flex-wrap gap-2 mb-3">
                                    @foreach($pokemon['abilities'] as $i => $ability)
                                        @php
                                            $isHidden = $ability['is_hidden'] ?? false;
                                            $abilityName = $ability['name_es'] ?? ucfirst(str_replace('-', ' ', $ability['name'] ?? $ability));
                                        @endphp
                                        <button type="button"
                                            class="btn ability-btn px-3 py-1 d-flex align-items-center"
                                            style="
                                                {{ $isHidden 
                                                    ? 'background: var(--midnight-steel); color: var(--golden-bloom);' 
                                                    : 'background: var(--golden-bloom); color: var(--midnight-abyss);' 
                                                }}
                                                font-weight:700; border: none;"
                                            data-index="{{ $i }}">
                                            {{ $abilityName }}
                                            @if($isHidden)
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
                    <!-- Grupos huevo -->
                    <div class="col-md-6">
                        <div class="bento-card egg-group-card h-100 d-flex flex-column">
                            <div class="card-body p-4 d-flex flex-column h-100">
                                <h5 class="card-title fw-bold mb-2" style="color: var(--golden-bloom);">Grupo huevo</h5>
                                <div class="mt-2">
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
                                            'undiscovered' => 'Desconocido',
                                            'water2' => 'Agua 2',
                                            'ditto' => 'Ditto',
                                            'dragon' => 'Dragón',
                                            'no-eggs' => 'Sin huevo',
                                        ];
                                    @endphp
                                    @foreach($pokemon['egg_groups'] as $group)
                                        @php
                                            $key = strtolower(is_array($group) ? ($group['name'] ?? $group[0] ?? '') : $group);
                                            $translated = $eggGroupTranslations[$key] ?? ucfirst(str_replace(['-', '_'], ' ', $key));
                                        @endphp
                                        <span class="badge bg-secondary me-2 mb-1">
                                            {{ $translated }}
                                        </span>
                                    @endforeach
                                </div>
                                <div class="flex-grow-1"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Back Button -->
        <div class="text-center mt-5">
            <a href="/pokedex" class="btn btn-danger btn-lg px-4 py-2">
                &larr; Volver a la Pokédex
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add delay to each stat bar animation for a cascading effect
        document.addEventListener('DOMContentLoaded', function() {
            const statBars = document.querySelectorAll('.stat-loading');
            
            statBars.forEach((bar, index) => {
                // Set initial state
                bar.style.transform = 'scaleX(0)';
                bar.style.opacity = '0.5';
                
                // Animate with delay
                setTimeout(() => {
                    bar.style.animation = `loadStat 0.8s cubic-bezier(0.22, 0.61, 0.36, 1) ${index * 0.1}s forwards`;
                }, 100);
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const abilityBtns = document.querySelectorAll('.ability-btn');
            const abilityDesc = document.getElementById('ability-description');
            const abilityData = @json($pokemon['abilities']);

            function selectAbility(idx) {
                const desc = abilityData[idx]?.description;
                abilityDesc.innerHTML = desc
                    ? `<span>${desc}</span>`
                    : `<span class="text-muted">No hay descripción disponible en español.</span>`;
                abilityBtns.forEach(b => b.classList.remove('active'));
                if (abilityBtns[idx]) abilityBtns[idx].classList.add('active');
            }

            abilityBtns.forEach((btn, idx) => {
                btn.addEventListener('click', function() {
                    selectAbility(idx);
                });
            });

            // Selecciona la primera habilidad por defecto si existe
            if (abilityBtns.length > 0) {
                selectAbility(0);
            }
        });
    </script>
</body>
</html>