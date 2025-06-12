@extends('layouts.app')

@section('title', config('app.name') . ' - Pokémon Aleatorio')

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elementos DOM
    const elements = {
        btn: document.getElementById('randomBtn'),
        result: document.getElementById('randomResult'),
        typeSelect: document.getElementById('typeSelect'),
        eggGroupSelect: document.getElementById('eggGroupSelect'),
        loader: document.getElementById('randomLoader'),
        qtySelect: document.getElementById('qtySelect')
    };

    // Traducciones de tipos
    const typeTranslations = {
        normal: 'Normal', fire: 'Fuego', water: 'Agua', electric: 'Eléctrico', 
        grass: 'Planta', ice: 'Hielo', fighting: 'Lucha', poison: 'Veneno', 
        ground: 'Tierra', flying: 'Volador', psychic: 'Psíquico', bug: 'Bicho', 
        rock: 'Roca', ghost: 'Fantasma', dragon: 'Dragón', dark: 'Siniestro', 
        steel: 'Acero', fairy: 'Hada'
    };

    // Función para obtener Pokémon aleatorio
    function fetchRandomPokemon() {
        elements.result.innerHTML = '';
        elements.loader.classList.remove('d-none');

        const params = {
            type: elements.typeSelect.value,
            eggGroup: elements.eggGroupSelect.value,
            qty: elements.qtySelect.value
        };

        fetch(`{{ url('/api/random-pokemons') }}?type=${encodeURIComponent(params.type)}&egg_group=${encodeURIComponent(params.eggGroup)}&qty=${params.qty}`)
            .then(res => {
                if (!res.ok) {
                    throw new Error(`HTTP error! status: ${res.status}`);
                }
                return res.json();
            })
            .then(data => {
                elements.loader.classList.add('d-none');
                console.log('Respuesta de la API:', data);
                
                if (data.success && data.data && data.data.length > 0) {
                    renderPokemonCards(data.data);
                } else {
                    showError('No se encontró ningún Pokémon con esos filtros o la API no devolvió datos válidos.', 'warning');
                }
            })
            .catch((error) => {
                elements.loader.classList.add('d-none');
                console.error("Error en la solicitud fetch de Pokémon aleatorio:", error);
                showError('Error al buscar Pokémon aleatorio. Por favor, inténtalo de nuevo.', 'danger');
            });
    }

    // Función para renderizar tarjetas de Pokémon
    function renderPokemonCards(pokemonList) {
        let cardsHtml = '';
        
        pokemonList.forEach((pokemon, index) => {
            const isShiny = Math.random() < (1 / 20); // Probabilidad shiny: 1/20
            let imgUrl = pokemon.image || '';
            
            if (isShiny && imgUrl.includes('/official-artwork/')) {
                imgUrl = imgUrl.replace('/official-artwork/', '/official-artwork/shiny/');
            }

            const delay = `${index * 0.1}s`;
            const cardDelay = `${index * 0.08}s`;

            cardsHtml += `
            <div class="card mx-auto bento-card mb-3 random-pokemon-card${isShiny ? ' ultra-shiny-glow' : ''}" style="cursor:pointer; animation-delay: ${cardDelay};">
                ${isShiny ? `<span class="ultra-shiny-star" title="¡Shiny!">★</span>` : ''}
                <span class="badge bg-secondary">#${String(pokemon.pokeapi_id || '0000').padStart(4, '0')}</span>
                <div class="pokemon-img-container d-flex justify-content-center align-items-center">
                    <img src="${imgUrl}"
                        alt="${pokemon.display_name || 'Pokémon'}"
                        class="pokemon-image img-fluid"
                        style="animation: pokemon-pop 0.6s ease-out forwards; animation-delay: ${delay};"
                        onerror="this.onerror=null;this.src='{{ asset('images/pokemon/placeholder.png') }}';">
                </div>
                <div class="card-body text-center">
                    <h3 class="fw-bold mb-1${isShiny ? ' golden-text' : ''}">
                        ${pokemon.display_name || 'Nombre Desconocido'}
                    </h3>
                    <div class="mb-2">
                        ${renderTypesBadges(pokemon.types)}
                    </div>
                    <a href="/pokemon/${pokemon.pokeapi_id || '#'}" class="btn btn-primary mt-2">Ver detalles</a>
                </div>
            </div>`;
        });

        elements.result.innerHTML = `<div class="random-pokemon-row">${cardsHtml}</div>`;
        
        // Animación de entrada
        setTimeout(() => {
            const container = elements.result.querySelector('.random-pokemon-row');
            if (container) {
                container.style.opacity = '1';
                container.style.transform = 'scale(1)';
            }
            elements.result.classList.add('random');

            // Evento hover para imágenes
            document.querySelectorAll('.random-pokemon-card img.pokemon-image').forEach(img => {
                img.addEventListener('mouseenter', function() {
                    this.style.animation = 'none';
                    void this.offsetWidth;
                    this.style.animation = '';
                });
            });
        }, 50);
    }

    // Función para renderizar badges de tipos
    function renderTypesBadges(types) {
        return (types || []).map(type => {
            const key = (type || '').toLowerCase();
            const label = typeTranslations[key] || (key ? key.charAt(0).toUpperCase() + key.slice(1) : 'Tipo Desconocido');
            return `<span class="type-badge ${key} mx-1">${label}</span>`;
        }).join('');
    }

    // Función para mostrar errores
    function showError(message, type = 'danger') {
        elements.result.innerHTML = `<div class="alert alert-${type} text-center">${message}</div>`;
        elements.result.classList.remove('random');
    }

    // Eventos
    elements.btn.addEventListener('click', fetchRandomPokemon);
});
</script>
@endpush

@section('content')
<div class="min-vh-100">
    <!-- Encabezado -->
    <div class="header-section text-center mb-4">
        <h1 class="display-5 fw-bold">Pokémon Aleatorio</h1>
        <p class="text-light">¿No sabes qué Pokémon ver? ¡Déjalo al azar! Puedes filtrar por tipo, grupo huevo y cantidad.</p>
    </div>

    <!-- Filtros - Ajustado para evitar desbordamiento -->
    <div class="container px-2">
        <div id="interaccion" class="row g-2 justify-content-center mb-4">
            <!-- Tipo -->
            <div class="col-12 col-sm-6 col-md-3">
                <select class="form-select" id="typeSelect">
                    <option value="all">Todos los tipos</option>
                    <option value="normal">Normal</option>
                    <option value="fire">Fuego</option>
                    <option value="water">Agua</option>
                    <option value="electric">Eléctrico</option>
                    <option value="grass">Planta</option>
                    <option value="ice">Hielo</option>
                    <option value="fighting">Lucha</option>
                    <option value="poison">Veneno</option>
                    <option value="ground">Tierra</option>
                    <option value="flying">Volador</option>
                    <option value="psychic">Psíquico</option>
                    <option value="bug">Bicho</option>
                    <option value="rock">Roca</option>
                    <option value="ghost">Fantasma</option>
                    <option value="dragon">Dragón</option>
                    <option value="dark">Siniestro</option>
                    <option value="steel">Acero</option>
                    <option value="fairy">Hada</option>
                </select>
            </div>
            
            <!-- Grupo huevo -->
            <div class="col-12 col-sm-6 col-md-3">
                <select class="form-select" id="eggGroupSelect">
                    <option value="all">Todos los grupos huevo</option>
                    <option value="monster">Monstruo</option>
                    <option value="water1">Agua 1</option>
                    <option value="bug">Bicho</option>
                    <option value="flying">Volador</option>
                    <option value="amorphous">Amorfo</option>
                    <option value="field">Campo</option>
                    <option value="fairy">Hada</option>
                    <option value="plant">Planta</option>
                    <option value="humanshape">Humanoide</option>
                    <option value="water3">Agua 3</option>
                    <option value="mineral">Mineral</option>
                    <option value="undiscovered">Desconocido</option>
                    <option value="water2">Agua 2</option>
                    <option value="ditto">Ditto</option>
                    <option value="dragon">Dragón</option>
                    <option value="no-eggs">Sin huevo</option>
                </select>
            </div>
            
            <!-- Cantidad -->
            <div class="col-6 col-sm-6 col-md-2">
                <select class="form-select" id="qtySelect">
                    @for ($i = 1; $i <= 6; $i++)
                        <option value="{{ $i }}" {{ $i == 6 ? 'selected' : '' }}>{{ $i }} Pokémon</option>
                    @endfor
                </select>
            </div>
            
            <!-- Botón -->
            <div class="col-6 col-sm-6 col-md-2">
                <button id="randomBtn" class="btn btn-primary w-100">
                    <i class="bi bi-shuffle"></i> ¡Generar!
                </button>
            </div>
        </div>
    </div>

    <!-- Loader -->
    <div class="d-flex justify-content-center mb-3">
        <div id="randomLoader" class="d-none">
            <div class="spinner-border text-golden-bloom" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
        </div>
    </div>

    <!-- Resultados -->
    <div id="randomResult" class="mt-3 container-fluid px-2"></div>
</div>

<style>
/* Estilos para asegurar que el contenido no se desborde */
#interaccion {
    max-width: 100%;
    margin-left: 0;
    margin-right: 0;
    align-items: center;
}

@media (max-width: 576px) {
    .random-pokemon-row {
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 0.5rem;
    }
    
    .random-pokemon-card h3 {
        font-size: 1rem;
    }
    
    .random-pokemon-card .btn {
        font-size: 0.8rem;
        padding: 0.25rem 0.5rem;
    }
}
</style>
@endsection