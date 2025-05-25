{{-- filepath: c:\xampp\htdocs\poketek\resources\views\pokemon\show.blade.php --}}
@extends('layouts.app')

@section('title', ucfirst($pokemon['display_name']) . ' - Pokédex')

@push('scripts')
    <script>
        // Animación de barras de stats
        document.addEventListener('DOMContentLoaded', function() {
            const statBars = document.querySelectorAll('.stat-loading');
            statBars.forEach((bar, index) => {
                bar.style.transform = 'scaleX(0)';
                bar.style.opacity = '0.5';
                setTimeout(() => {
                    bar.style.animation = `loadStat 0.8s cubic-bezier(0.22, 0.61, 0.36, 1) ${index * 0.1}s forwards`;
                }, 100);
            });

            // Alternar entre imagen normal y shiny al hacer click, con animación
            const pokeImg = document.querySelector('.pokemon-image');
            if (pokeImg) {
                let isShiny = false;
                const normalUrl = pokeImg.src;
                const match = normalUrl.match(/\/(\d+)\.png$/);
                const pokeNum = match ? match[1] : null;
                const shinyUrl = pokeNum ?
                    normalUrl.replace('/official-artwork/', '/official-artwork/shiny/') :
                    normalUrl;

                pokeImg.style.cursor = 'pointer';
                pokeImg.title = 'Haz clic para ver la versión shiny';

                pokeImg.addEventListener('click', function() {
                    isShiny = !isShiny;
                    // Animación de desvanecimiento
                    pokeImg.style.transition = 'opacity 0.25s';
                    pokeImg.style.opacity = '0';
                    setTimeout(() => {
                        pokeImg.src = isShiny ? shinyUrl : normalUrl;
                        pokeImg.title = isShiny ?
                            'Haz clic para ver la versión normal' :
                            'Haz clic para ver la versión shiny';
                    }, 220);
                });

                // Cuando la imagen termine de cargar, aplica animación de pop y aparece
                pokeImg.addEventListener('load', function() {
                    pokeImg.style.transition = 'none';
                    pokeImg.style.opacity = '1';
                    pokeImg.style.animation = 'pokemon-pop 0.6s cubic-bezier(0.22, 0.61, 0.36, 1)';
                    setTimeout(() => {
                        pokeImg.style.animation = '';
                    }, 600);
                });
            }
        });

        // Habilidades: selección y descripción
        document.addEventListener('DOMContentLoaded', function() {
            const abilityBtns = document.querySelectorAll('.ability-btn');
            const abilityDesc = document.getElementById('ability-description');
            const abilityData = @json($pokemon['abilities']);

            function selectAbility(idx) {
                const desc = abilityData[idx]?.description;
                abilityDesc.innerHTML = desc ?
                    `<span>${desc}</span>` :
                    `<span class="text-muted">No hay descripción disponible en español.</span>`;
                abilityBtns.forEach(b => b.classList.remove('active'));
                if (abilityBtns[idx]) abilityBtns[idx].classList.add('active');
            }

            abilityBtns.forEach((btn, idx) => {
                btn.addEventListener('click', function() {
                    selectAbility(idx);
                });
            });

            if (abilityBtns.length > 0) {
                selectAbility(0);
            }
        });
    </script>
@endpush

@section('content')
    <div class="container pokemon-detail-container">
        <div class="header-section text-center py-4 px-3">
            <h1 class="display-4 fw-bold">
                {{ ucfirst($pokemon['display_name']) }}
                <span class="pokemon-id-badge">#{{ str_pad($pokemon['id'], 4, '0', STR_PAD_LEFT) }}</span>
            </h1>
            <div class="d-flex justify-content-center mt-3 flex-wrap">
                @php
                    $typeTranslations = [
                        'normal' => 'Normal', 'fire' => 'Fuego', 'water' => 'Agua', 'electric' => 'Eléctrico',
                        'grass' => 'Planta', 'ice' => 'Hielo', 'fighting' => 'Lucha', 'poison' => 'Veneno',
                        'ground' => 'Tierra', 'flying' => 'Volador', 'psychic' => 'Psíquico', 'bug' => 'Bicho',
                        'rock' => 'Roca', 'ghost' => 'Fantasma', 'dragon' => 'Dragón', 'dark' => 'Siniestro',
                        'steel' => 'Acero', 'fairy' => 'Hada',
                    ];
                @endphp
                @foreach ($pokemon['types'] as $type)
                    @php
                        $typeName = is_array($type) ? $type[0] : $type;
                        $translatedType = $typeTranslations[strtolower($typeName)] ?? ucfirst($typeName);
                    @endphp
                    <span class="type-badge {{ strtolower($typeName) }} mx-1 my-1">{{ $translatedType }}</span>
                @endforeach
            </div>
        </div>

        <div class="row g-4 bento-mosaic align-items-stretch">
            <!-- Columna Izquierda: Imagen (más grande) + Grupo huevo + Motes, todos mismo ancho -->
            <div class="col-12 col-lg-5 d-flex flex-column gap-4 align-items-center">
                <div style="width:100%;max-width:340px;">
                    <div class="bento-card pokemon-img-container bento-img-square flex-grow-0 d-flex align-items-center justify-content-center mb-0" style="min-height:320px;max-height:380px;">
                        <img src="{{ $pokemon['image'] }}" class="pokemon-image img-fluid mx-auto d-block"
                            alt="{{ $pokemon['display_name'] }}" loading="lazy" style="max-width: 100%; max-height: 340px;">
                    </div>
                    <div class="bento-card egg-group-card flex-grow-0 d-flex flex-column justify-content-center text-center h-auto mt-4">
                        <div class="card-body p-3 d-flex flex-column align-items-center justify-content-center">
                            <h5 class="card-title fw-bold mb-2" style="color: var(--golden-bloom);">Grupo huevo</h5>
                            <div class="mt-2 d-flex flex-wrap gap-1 justify-content-center">
                                @php
                                    $eggGroupTranslations = [
                                        'monster' => 'Monstruo', 'water1' => 'Agua 1', 'bug' => 'Bicho',
                                        'flying' => 'Volador', 'amorphous' => 'Amorfo', 'field' => 'Campo',
                                        'fairy' => 'Hada', 'plant' => 'Planta', 'humanshape' => 'Humanoide',
                                        'water3' => 'Agua 3', 'mineral' => 'Mineral', 'undiscovered' => 'Desconocido',
                                        'water2' => 'Agua 2', 'ditto' => 'Ditto', 'dragon' => 'Dragón', 'no-eggs' => 'Sin huevo',
                                    ];
                                @endphp
                                @foreach ($pokemon['egg_groups'] as $group)
                                    @php
                                        $key = strtolower(is_array($group) ? ($group['name'] ?? $group[0] ?? '') : $group);
                                        $translated = $eggGroupTranslations[$key] ?? ucfirst(str_replace(['-', '_'], ' ', $key));
                                    @endphp
                                    <span class="badge bg-secondary me-2 mb-1">
                                        {{ $translated }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="bento-card nicknames-card flex-grow-0 d-flex flex-column justify-content-center text-center mt-4">
                        <div class="card-body p-3 d-flex flex-column align-items-center justify-content-center">
                            <h5 class="card-title fw-bold mb-2" style="color: var(--golden-bloom);">Motes</h5>
                            <div class="mt-2 text-light small">
                                @auth
                                    @if (!empty($pokemon['nicknames']) && count($pokemon['nicknames']) > 0)
                                        <ul class="list-unstyled mb-0">
                                            @foreach ($pokemon['nicknames'] as $nickname)
                                                <li><span class="badge bg-info text-dark me-1">{{ $nickname }}</span></li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p class="text-light">No has añadido motes para este Pokémon.</p>
                                    @endif
                                @else
                                    <p class="text-light">Inicia sesión para añadir y ver tus motes.</p>
                                    <a href="{{ route('login') }}" class="btn btn-sm mt-2"
                                        style="background-color: var(--golden-bloom); color: var(--midnight-abyss); font-weight: 700;">Iniciar
                                        Sesión</a>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna Derecha: Descripción + Habilidades + Estadísticas -->
            <div class="col-12 col-lg-7 d-flex flex-column gap-4">
                <div class="row g-4">
                    <!-- Descripción (pequeña, arriba) -->
                    <div class="col-12">
                        <div class="bento-card description-card flex-grow-0 d-flex flex-column h-auto">
                            <div class="card-body p-3 d-flex flex-column">
                                <h5 class="card-title fw-bold mb-2" style="color: var(--golden-bloom);">Descripción</h5>
                                <div id="pokemon-description" class="text-light small mt-auto" style="min-height:2.5em;">
                                    <span>
                                        {{ $pokemon['description'] ?? 'Sin descripción.' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Habilidades (toda la fila, botones seguidos) -->
                    <div class="col-12">
                        <div class="bento-card ability-card flex-grow-0 d-flex flex-column h-auto">
                            <div class="card-body p-3 d-flex flex-column h-100">
                                <h5 class="card-title fw-bold mb-2" style="color: var(--golden-bloom);">Habilidades</h5>
                                <div id="abilities-list" class="d-flex flex-row flex-wrap gap-2 mb-3">
                                    @foreach ($pokemon['abilities'] as $i => $ability)
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
                    <!-- Estadísticas ocupa el resto -->
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
                                            $translatedStat = $statTranslations[$statName] ?? ucfirst(str_replace('-', ' ', $statName));
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
                                                    aria-valuemin="0" aria-valuemax="255" data-percentage="{{ $percentage }}">
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
