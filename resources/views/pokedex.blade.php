@extends('layouts.app')

@section('title', 'Pokétek - Pokédex Nacional')

@push('scripts')
    <script src="{{ asset('js/pokedex-scripts.js') }}" defer></script>
        
    </script>
@endpush

@section('content')
    <div class="loader-container" id="initialLoader">
        <div class="loader"></div>
    </div>

    <div class="header-section text-center py-4 px-3">
        <h1 class="display-4 fw-bold mb-0">Pokédex Nacional</h1>
    </div>

    <div class="container">
        <!-- Filtros y buscador -->
        <div class="filter-section row justify-content-center mb-4">
            <div class="col-12 col-md-4 mb-2 mb-md-0">
                <input type="text" id="searchInput" class="form-control"
                    placeholder="Buscar por nombre..." autocomplete="off">
            </div>
            <div class="col-6 col-md-3 mb-2 mb-md-0">
                @php
                    $translations = [
                        'types' => [
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
                        ],
                        'egg_groups' => [
                            'monster' => 'Monstruo',
                            'water 1' => 'Agua 1',
                            'bug' => 'Bicho',
                            'flying' => 'Volador',
                            'amorphous' => 'Amorfo',
                            'field' => 'Campo',
                            'fairy' => 'Hada',
                            'plant' => 'Planta',
                            'humanshape' => 'Humanoide',
                            'water 3' => 'Agua 3',
                            'mineral' => 'Mineral',
                            'undiscovered' => 'Desconocido',
                            'water 2' => 'Agua 2',
                            'ditto' => 'Ditto',
                            'dragon' => 'Dragón',
                            'no-eggs' => 'Sin huevo',
                        ]
                    ];
                @endphp

                <select class="form-select" id="typeSelect">
                    <option value="all">Todos los tipos</option>
                    @foreach($translations['types'] as $en => $es)
                        <option value="{{ $en }}">{{ $es }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-3">
                <select class="form-select" id="eggGroupSelect">
                    <option value="all">Todos los grupos huevo</option>
                    @foreach($translations['egg_groups'] as $en => $es)
                        <option value="{{ $en }}">{{ $es }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Grid de Pokémon -->
        <div class="row g-5" id="pokemonGrid">
            @foreach ($pokemons as $pokemon)
                @php
                    $pokemonId = $pokemon->pokeapi_id;
                    if ($pokemonId > 1025)
                        continue;

                    $imageUrl = $pokemon->image;
                    $displayName = str_replace('-', ' ', $pokemon->name);
                    $types = $pokemon->types;
                    $eggGroups = $pokemon->egg_groups;
                @endphp

                <div class="col-6 col-sm-4 col-md-3 col-lg-2 pokemon-item" data-id="{{ $pokemonId }}"
                    data-name="{{ $pokemon->name }}" data-types="{{ json_encode($types) }}"
                    data-egg-groups="{{ json_encode($eggGroups) }}" style="display: none;">
                    <a href="/pokemon/{{ $pokemonId }}" class="text-decoration-none">
                        <div class="card pokemon-card h-100 shadow-sm">
                            <div class="pokemon-img-container p-3">
                                <span class="pokemon-number-bg">
                                    #{{ str_pad($pokemonId, 4, '0', STR_PAD_LEFT) }}
                                </span>
                                <img src="{{ $imageUrl }}" alt="{{ $displayName }}" class="pokemon-img loaded" loading="lazy">
                            </div>
                            <div class="card-body text-center">
                                <h5 class="pokemon-name mb-1">{{ ucwords($displayName) }}</h5>
                                <p class="pokemon-number text-muted small d-none">
                                    #{{ str_pad($pokemonId, 4, '0', STR_PAD_LEFT) }}</p>

                                @if (!empty($types))
                                    <div class="d-flex justify-content-center flex-wrap gap-1 mt-2">
                                        @foreach ($types as $type)
                                            @php
                                                $typeName = is_array($type) ? ($type['name'] ?? $type[0] ?? '') : $type;
                                                $translated = $translations['types'][strtolower($typeName)] ?? ucfirst($typeName);
                                            @endphp
                                            <span class="type-badge {{ strtolower($typeName) }}">{{ $translated }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

        <!-- Paginación original (servidor) -->
        <nav class="mt-5 pagination-container" id="originalPagination">
            <ul class="pagination justify-content-center">
                @php
                    $genQuery = '';
                @endphp

                <li class="page-item {{ $currentPage == 1 ? 'disabled' : '' }}">
                    <a class="page-link" href="?page={{ $currentPage - 1 }}{{ $genQuery }}">«</a>
                </li>

                @if ($currentPage > 3)
                    <li class="page-item"><a class="page-link" href="?page=1{{ $genQuery }}">1</a></li>
                    @if ($currentPage > 4)
                        <li class="page-item disabled"><span class="page-link">…</span></li>
                    @endif
                @endif

                @for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++)
                    <li class="page-item {{ $i == $currentPage ? 'active' : '' }}">
                        <a class="page-link" href="?page={{ $i }}{{ $genQuery }}">{{ $i }}</a>
                    </li>
                @endfor

                @if ($currentPage < $totalPages - 2)
                    @if ($currentPage < $totalPages - 3)
                        <li class="page-item disabled"><span class="page-link">…</span></li>
                    @endif
                    <li class="page-item"><a class="page-link"
                            href="?page={{ $totalPages }}{{ $genQuery }}">{{ $totalPages }}</a></li>
                @endif

                <li class="page-item {{ $currentPage == $totalPages ? 'disabled' : '' }}">
                    <a class="page-link" href="?page={{ $currentPage + 1 }}{{ $genQuery }}">»</a>
                </li>
            </ul>
        </nav>

        <!-- Paginación para resultados filtrados (cliente) -->
        <nav class="mt-5 filtered-pagination" id="filteredPagination" style="display: none;">
            <ul class="pagination justify-content-center" id="filteredPaginationList">
                <!-- La paginación se generará dinámicamente con JavaScript -->
            </ul>
        </nav>
    </div>
@endsection