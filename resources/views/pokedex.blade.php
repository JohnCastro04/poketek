<!DOCTYPE html>
<html lang="es">

<head>
    @vite(['resources/js/app.js'])
    <meta charset="UTF-8">
    <title>Pokétek - Pokédex Nacional</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>
    @include('layouts.header')
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
                    placeholder="Buscar Pokémon por nombre o número..." autocomplete="off">
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
                    if ($pokemonId > 1025) continue;
                    
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
                                <img src="{{ $imageUrl }}" alt="{{ $displayName }}" class="pokemon-img" loading="lazy">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const initialLoader = document.getElementById('initialLoader');
            const searchInput = document.getElementById('searchInput');
            const typeSelect = document.getElementById('typeSelect');
            const eggGroupSelect = document.getElementById('eggGroupSelect');
            const pokemonGrid = document.getElementById('pokemonGrid');
            const originalPagination = document.getElementById('originalPagination');
            const filteredPagination = document.getElementById('filteredPagination');
            const filteredPaginationList = document.getElementById('filteredPaginationList');
            
            let typingTimer;
            const itemsPerPage = 24;
            let currentFilteredPage = 1;
            let filteredPokemons = [];

            // Traducciones para tipos y grupos huevo (usando las mismas que en PHP)
            const translations = {
                types: {
                    'normal': 'Normal', 'fire': 'Fuego', 'water': 'Agua', 'electric': 'Eléctrico',
                    'grass': 'Planta', 'ice': 'Hielo', 'fighting': 'Lucha', 'poison': 'Veneno',
                    'ground': 'Tierra', 'flying': 'Volador', 'psychic': 'Psíquico', 'bug': 'Bicho',
                    'rock': 'Roca', 'ghost': 'Fantasma', 'dragon': 'Dragón', 'dark': 'Siniestro',
                    'steel': 'Acero', 'fairy': 'Hada'
                },
                egg_groups: {
                    'monster': 'Monstruo', 'water 1': 'Agua 1', 'bug': 'Bicho', 'flying': 'Volador',
                    'amorphous': 'Amorfo', 'field': 'Campo', 'fairy': 'Hada', 'plant': 'Planta',
                    'humanshape': 'Humanoide', 'water 3': 'Agua 3', 'mineral': 'Mineral',
                    'undiscovered': 'Desconocido', 'water 2': 'Agua 2', 'ditto': 'Ditto',
                    'dragon': 'Dragón', 'no-eggs': 'Sin huevo'
                }
            };

            function translateType(type) {
                return translations.types[type.toLowerCase()] || type;
            }

            function translateEggGroup(eggGroup) {
                return translations.egg_groups[eggGroup.toLowerCase().replace(' ', '-')] || eggGroup;
            }

            function updateUrl() {
                const params = new URLSearchParams();
                if (searchInput.value.trim()) params.set('query', searchInput.value.trim());
                if (typeSelect.value !== 'all') params.set('type', typeSelect.value);
                if (eggGroupSelect.value !== 'all') params.set('egg_group', eggGroupSelect.value);
                if (currentFilteredPage > 1) params.set('filtered_page', currentFilteredPage);
                const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
                window.history.replaceState({}, '', newUrl);
            }

            function filterPokemons() {
                const searchTerm = searchInput.value.trim().toLowerCase();
                const selectedType = typeSelect.value;
                const selectedEggGroup = eggGroupSelect.value;

                const pokemonItems = document.querySelectorAll('.pokemon-item');
                filteredPokemons = [];

                pokemonItems.forEach(item => {
                    const name = item.dataset.name.toLowerCase();
                    const types = JSON.parse(item.dataset.types || '[]');
                    const eggGroups = JSON.parse(item.dataset.eggGroups || '[]');

                    const typeNames = types.map(t => typeof t === 'string' ? t : (t.name || '')).map(t => t.toLowerCase());
                    const eggGroupNames = eggGroups.map(eg => typeof eg === 'string' ? eg : (eg.name || eg)).map(eg => eg.toLowerCase().replace(/[\s\-]/g, ''));

                    // Solo buscar por nombre
                    const nameMatch = !searchTerm || name.includes(searchTerm);
                    const typeMatch = selectedType === 'all' || typeNames.includes(selectedType);
                    const normalizedSelectedEggGroup = selectedEggGroup === 'all'
                        ? 'all'
                        : selectedEggGroup.toLowerCase().replace(/[\s\-]/g, '');
                    const eggGroupMatch = selectedEggGroup === 'all' ||
                        eggGroupNames.some(eg => eg === normalizedSelectedEggGroup);

                    if (nameMatch && typeMatch && eggGroupMatch) {
                        filteredPokemons.push(item);
                    }
                });

                filteredPokemons.sort((a, b) => parseInt(a.dataset.id) - parseInt(b.dataset.id));

                const hasFilters = searchTerm || selectedType !== 'all' || selectedEggGroup !== 'all';
                originalPagination.style.display = hasFilters ? 'none' : 'block';
                filteredPagination.style.display = hasFilters ? 'block' : 'none';

                displayFilteredResults();
                updateUrl();
            }

            function displayFilteredResults() {
                document.querySelectorAll('.pokemon-item').forEach(item => {
                    item.style.display = 'none';
                });

                const startIndex = (currentFilteredPage - 1) * itemsPerPage;
                const endIndex = startIndex + itemsPerPage;
                const paginatedItems = filteredPokemons.slice(startIndex, endIndex);

                paginatedItems.forEach(item => {
                    item.style.display = 'block';
                });

                if (filteredPokemons.length === 0) {
                    const noResults = document.createElement('div');
                    noResults.className = 'col-12 text-center text-muted py-5 no-results-message';
                    noResults.textContent = 'No se encontraron Pokémon que coincidan con los filtros.';

                    const existingNoResults = pokemonGrid.querySelector('.no-results-message');
                    if (!existingNoResults) {
                        pokemonGrid.appendChild(noResults);
                    }
                } else {
                    const noResults = pokemonGrid.querySelector('.no-results-message');
                    if (noResults) {
                        noResults.remove();
                    }
                }

                updateFilteredPagination();
            }

            function updateFilteredPagination() {
                const totalPages = Math.ceil(filteredPokemons.length / itemsPerPage);
                filteredPaginationList.innerHTML = '';

                if (totalPages <= 1) {
                    filteredPagination.style.display = 'none';
                    return;
                }

                // Botón Anterior
                const prevLi = document.createElement('li');
                prevLi.className = `page-item ${currentFilteredPage === 1 ? 'disabled' : ''}`;
                prevLi.innerHTML = `<a class="page-link" href="#" data-page="${currentFilteredPage - 1}">«</a>`;
                prevLi.addEventListener('click', (e) => {
                    e.preventDefault();
                    if (currentFilteredPage > 1) {
                        currentFilteredPage--;
                        displayFilteredResults();
                    }
                });
                filteredPaginationList.appendChild(prevLi);

                // Páginas
                const maxVisiblePages = 5;
                let startPage = Math.max(1, currentFilteredPage - Math.floor(maxVisiblePages / 2));
                let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

                if (endPage - startPage + 1 < maxVisiblePages) {
                    startPage = Math.max(1, endPage - maxVisiblePages + 1);
                }

                if (startPage > 1) {
                    const firstLi = document.createElement('li');
                    firstLi.className = 'page-item';
                    firstLi.innerHTML = `<a class="page-link" href="#" data-page="1">1</a>`;
                    firstLi.addEventListener('click', (e) => {
                        e.preventDefault();
                        currentFilteredPage = 1;
                        displayFilteredResults();
                    });
                    filteredPaginationList.appendChild(firstLi);

                    if (startPage > 2) {
                        const ellipsisLi = document.createElement('li');
                        ellipsisLi.className = 'page-item disabled';
                        ellipsisLi.innerHTML = '<span class="page-link">…</span>';
                        filteredPaginationList.appendChild(ellipsisLi);
                    }
                }

                for (let i = startPage; i <= endPage; i++) {
                    const pageLi = document.createElement('li');
                    pageLi.className = `page-item ${i === currentFilteredPage ? 'active' : ''}`;
                    pageLi.innerHTML = `<a class="page-link" href="#" data-page="${i}">${i}</a>`;
                    pageLi.addEventListener('click', (e) => {
                        e.preventDefault();
                        currentFilteredPage = i;
                        displayFilteredResults();
                    });
                    filteredPaginationList.appendChild(pageLi);
                }

                if (endPage < totalPages) {
                    if (endPage < totalPages - 1) {
                        const ellipsisLi = document.createElement('li');
                        ellipsisLi.className = 'page-item disabled';
                        ellipsisLi.innerHTML = '<span class="page-link">…</span>';
                        filteredPaginationList.appendChild(ellipsisLi);
                    }

                    const lastLi = document.createElement('li');
                    lastLi.className = 'page-item';
                    lastLi.innerHTML = `<a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a>`;
                    lastLi.addEventListener('click', (e) => {
                        e.preventDefault();
                        currentFilteredPage = totalPages;
                        displayFilteredResults();
                    });
                    filteredPaginationList.appendChild(lastLi);
                }

                // Botón Siguiente
                const nextLi = document.createElement('li');
                nextLi.className = `page-item ${currentFilteredPage === totalPages ? 'disabled' : ''}`;
                nextLi.innerHTML = `<a class="page-link" href="#" data-page="${currentFilteredPage + 1}">»</a>`;
                nextLi.addEventListener('click', (e) => {
                    e.preventDefault();
                    if (currentFilteredPage < totalPages) {
                        currentFilteredPage++;
                        displayFilteredResults();
                    }
                });
                filteredPaginationList.appendChild(nextLi);
            }

            // Event listeners
            searchInput.addEventListener('input', function () {
                clearTimeout(typingTimer);
                currentFilteredPage = 1;
                typingTimer = setTimeout(filterPokemons, 300);
            });

            typeSelect.addEventListener('change', function() {
                currentFilteredPage = 1;
                filterPokemons();
            });

            eggGroupSelect.addEventListener('change', function() {
                currentFilteredPage = 1;
                filterPokemons();
            });

            // Cargar filtros desde la URL
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('type')) typeSelect.value = urlParams.get('type');
            if (urlParams.has('egg_group')) eggGroupSelect.value = urlParams.get('egg_group');
            if (urlParams.has('query')) searchInput.value = urlParams.get('query');
            if (urlParams.has('filtered_page')) currentFilteredPage = parseInt(urlParams.get('filtered_page')) || 1;

            if (!urlParams.has('query') && !urlParams.has('type') && !urlParams.has('egg_group')) {
                originalPagination.style.display = 'block';
                filteredPagination.style.display = 'none';
                document.querySelectorAll('.pokemon-item').forEach((item, index) => {
                    if (index < itemsPerPage) {
                        item.style.display = 'block';
                    }
                });
            } else {
                setTimeout(() => {
                    filterPokemons();
                }, 100);
            }

            setTimeout(() => {
                initialLoader.style.display = 'none';
            }, 300);
        });
    </script>
</body>
</html>