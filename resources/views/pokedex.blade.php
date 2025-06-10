@extends('layouts.app')

@section('title', config('app.name') . ' - Pokédex Nacional')

@push('scripts')
    <script src="{{ asset('js/pokedex-scripts.js') }}" defer></script>
@endpush

@section('content')
    <div class="loader-container" id="initialLoader">
        <div class="loader"></div>
    </div>

    <div class="header-section text-center py-4 px-3">
        <h1 class="display-4 fw-bold mb-0">Pokédex Nacional</h1>
    </div>

    <div class="container">
        <div class="filter-section row justify-content-center mb-4">
            <div class="col-12 col-md-4 mb-2 mb-md-0">
                <input type="text" id="searchInput" class="form-control"
                    placeholder="Buscar por nombre..." autocomplete="off" value="{{ $filters['query'] ?? '' }}">
            </div>
            <div class="col-6 col-md-3 mb-2 mb-md-0">
                @php
                    $translations = [
                        'types' => [
                            'normal' => 'Normal', 'fire' => 'Fuego', 'water' => 'Agua', 'electric' => 'Eléctrico',
                            'grass' => 'Planta', 'ice' => 'Hielo', 'fighting' => 'Lucha', 'poison' => 'Veneno',
                            'ground' => 'Tierra', 'flying' => 'Volador', 'psychic' => 'Psíquico', 'bug' => 'Bicho',
                            'rock' => 'Roca', 'ghost' => 'Fantasma', 'dragon' => 'Dragón', 'dark' => 'Siniestro',
                            'steel' => 'Acero', 'fairy' => 'Hada'
                        ],
                        'egg_groups' => [
                            'monster' => 'Monstruo', 'water 1' => 'Agua 1', 'bug' => 'Bicho', 'flying' => 'Volador',
                            'amorphous' => 'Amorfo', 'field' => 'Campo', 'fairy' => 'Hada', 'plant' => 'Planta',
                            'humanshape' => 'Humanoide', 'water 3' => 'Agua 3', 'mineral' => 'Mineral',
                            'undiscovered' => 'Desconocido', 'water 2' => 'Agua 2', 'ditto' => 'Ditto',
                            'dragon' => 'Dragón', 'no-eggs' => 'Sin huevo'
                        ]
                    ];
                @endphp

                <select class="form-select" id="typeSelect">
                    <option value="all">Todos los tipos</option>
                    @foreach($translations['types'] as $en => $es)
                        <option value="{{ $en }}" {{ ($filters['type'] ?? '') === $en ? 'selected' : '' }}>{{ $es }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-3">
                <select class="form-select" id="eggGroupSelect">
                    <option value="all">Todos los grupos huevo</option>
                    @foreach($translations['egg_groups'] as $en => $es)
                        <option value="{{ $en }}" {{ ($filters['egg_group'] ?? '') === $en ? 'selected' : '' }}>{{ $es }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-5 row-cols-xl-6 row-cols-xxl-8 g-3 g-md-4" id="pokemonGrid">
            @forelse ($pokemons as $pokemon)
                @php
                    $pokemonId = $pokemon->pokeapi_id;
                    $imageUrl = $pokemon->image;
                    $displayName = str_replace('-', ' ', $pokemon->name);
                    $types = $pokemon->types;
                    $eggGroups = $pokemon->egg_groups;
                @endphp

                <div class="col pokemon-item"
                    data-id="{{ $pokemonId }}"
                    data-name="{{ $pokemon->name }}"
                    data-types="{{ json_encode($types) }}"
                    data-egg-groups="{{ json_encode($eggGroups) }}">
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
            @empty
                <div class="col-12 text-center text-muted py-5">
                    No se encontraron Pokémon que coincidan con los filtros.
                </div>
            @endforelse
        </div>

        {{-- Paginación personalizada --}}
        <nav class="mt-5">
            @if ($pokemons->lastPage() > 1)
                <ul class="pagination justify-content-center">
                    {{-- Botón anterior --}}
                    <li class="page-item {{ $pokemons->onFirstPage() ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ $pokemons->previousPageUrl() ?? '#' }}" tabindex="-1" aria-disabled="{{ $pokemons->onFirstPage() ? 'true' : 'false' }}">
                            &laquo;
                        </a>
                    </li>

                    {{-- Páginas --}}
                    @php
                        $start = max(1, $pokemons->currentPage() - 2);
                        $end = min($pokemons->lastPage(), $pokemons->currentPage() + 2);
                        if ($end - $start < 4) {
                            $start = max(1, $end - 4);
                            $end = min($pokemons->lastPage(), $start + 4);
                        }
                    @endphp

                    @if ($start > 1)
                        <li class="page-item"><a class="page-link" href="{{ $pokemons->url(1) }}">1</a></li>
                        @if ($start > 2)
                            <li class="page-item disabled"><span class="page-link">…</span></li>
                        @endif
                    @endif

                    @for ($i = $start; $i <= $end; $i++)
                        <li class="page-item {{ $pokemons->currentPage() == $i ? 'active' : '' }}">
                            <a class="page-link" href="{{ $pokemons->url($i) }}">{{ $i }}</a>
                        </li>
                    @endfor

                    @if ($end < $pokemons->lastPage())
                        @if ($end < $pokemons->lastPage() - 1)
                            <li class="page-item disabled"><span class="page-link">…</span></li>
                        @endif
                        <li class="page-item"><a class="page-link" href="{{ $pokemons->url($pokemons->lastPage()) }}">{{ $pokemons->lastPage() }}</a></li>
                    @endif

                    {{-- Botón siguiente --}}
                    <li class="page-item {{ !$pokemons->hasMorePages() ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ $pokemons->nextPageUrl() ?? '#' }}" aria-disabled="{{ !$pokemons->hasMorePages() ? 'true' : 'false' }}">
                            &raquo;
                        </a>
                    </li>
                </ul>
            @endif
        </nav>
    </div>
@endsection