document.addEventListener('DOMContentLoaded', function () {
    const initialLoader = document.getElementById('initialLoader');
    const searchInput = document.getElementById('searchInput');
    const typeSelect = document.getElementById('typeSelect');
    const eggGroupSelect = document.getElementById('eggGroupSelect');

    let typingTimer;
    const doneTypingInterval = 300; // milliseconds

    function applyFilters() {
        const params = new URLSearchParams(window.location.search);

        const searchTerm = searchInput.value.trim();
        const selectedType = typeSelect.value;
        const selectedEggGroup = eggGroupSelect.value;

        // Limpia parámetros existentes y añade los nuevos
        params.delete('page'); // Siempre reinicia la página cuando se aplican filtros
        params.delete('query');
        params.delete('type');
        params.delete('egg_group');

        if (searchTerm) {
            params.set('query', searchTerm);
        }
        if (selectedType !== 'all') {
            params.set('type', selectedType);
        }
        if (selectedEggGroup !== 'all') {
            params.set('egg_group', selectedEggGroup);
        }

        // Redirige a la nueva URL con los filtros
        window.location.href = window.location.pathname + '?' + params.toString();
    }

    // Event listeners para los filtros
    searchInput.addEventListener('input', function () {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(applyFilters, doneTypingInterval);
    });

    typeSelect.addEventListener('change', applyFilters);
    eggGroupSelect.addEventListener('change', applyFilters);

    // Ocultar el loader inicial una vez que el DOM esté listo
    setTimeout(() => {
        initialLoader.style.display = 'none';
    }, 300); // Pequeño retraso para asegurar que la página se ha renderizado
});