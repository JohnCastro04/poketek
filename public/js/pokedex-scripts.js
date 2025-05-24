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

    typeSelect.addEventListener('change', function () {
        currentFilteredPage = 1;
        filterPokemons();
    });

    eggGroupSelect.addEventListener('change', function () {
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