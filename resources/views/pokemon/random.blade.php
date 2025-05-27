@extends('layouts.app')

@section('title', config('app.name') . ' - Pokémon Aleatorio')

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const btn = document.getElementById('randomBtn');
            const result = document.getElementById('randomResult');
            const typeSelect = document.getElementById('typeSelect');
            const eggGroupSelect = document.getElementById('eggGroupSelect');
            const loader = document.getElementById('randomLoader');
            const qtySelect = document.getElementById('qtySelect');

            const typeTranslations = {
                normal: 'Normal', fire: 'Fuego', water: 'Agua', electric: 'Eléctrico', grass: 'Planta',
                ice: 'Hielo', fighting: 'Lucha', poison: 'Veneno', ground: 'Tierra', flying: 'Volador',
                psychic: 'Psíquico', bug: 'Bicho', rock: 'Roca', ghost: 'Fantasma', dragon: 'Dragón',
                dark: 'Siniestro', steel: 'Acero', fairy: 'Hada'
            };

            function fetchRandomPokemon() {
                result.innerHTML = '';
                loader.classList.remove('d-none');

                const type = typeSelect.value;
                const eggGroup = eggGroupSelect.value;
                const qty = qtySelect.value;

                fetch(`{{ url('/api/random-pokemon') }}?type=${encodeURIComponent(type)}&egg_group=${encodeURIComponent(eggGroup)}&qty=${qty}`)
                    .then(res => {
                        // Comprobación de que la respuesta es OK (status 200)
                        if (!res.ok) {
                            // Si la respuesta no es OK, lanza un error para que lo capture el .catch()
                            throw new Error(`HTTP error! status: ${res.status}`);
                        }
                        return res.json();
                    })
                    .then(data => {
                        loader.classList.add('d-none');

                        // *** Diagnóstico: Muestra la respuesta completa de la API ***
                        console.log('Respuesta de la API:', data);

                        if (data.success && data.data && data.data.length > 0) {
                            let cardsHtml = '';
                            data.data.forEach((p, index) => {
                                // Probabilidad shiny: 1/50
                                const isShiny = Math.random() < (1 / 20);

                                // Recupera la imagen normal y modifica la ruta si es shiny
                                let imgUrl = p.image || '';
                                if (isShiny && imgUrl.includes('/official-artwork/')) {
                                    imgUrl = imgUrl.replace('/official-artwork/', '/official-artwork/shiny/');
                                }

                                const delay = `${index * 0.1}s`;
                                const cardDelay = `${index * 0.08}s`;

                                cardsHtml += `
                                <div class="card mx-auto bento-card mb-3 random-pokemon-card${isShiny ? ' ultra-shiny-glow' : ''}" style="cursor:pointer; animation-delay: ${cardDelay};">
                                    ${isShiny ? `<span class="ultra-shiny-star" title="¡Shiny!">★</span>` : ''}
                                    <span class="badge bg-secondary">#${String(p.id || '0000').padStart(4, '0')}</span>
                                    <div class="pokemon-img-container d-flex justify-content-center align-items-center">
                                        <img src="${imgUrl}"
                                             alt="${p.display_name || 'Pokémon'}"
                                             class="pokemon-image img-fluid"
                                             style="animation: pokemon-pop 0.6s ease-out forwards; animation-delay: ${delay};"
                                             onerror="this.onerror=null;this.src='{{ asset('images/pokemon/placeholder.png') }}';">
                                    </div>
                                    <div class="card-body text-center">
                                        <h3 class="fw-bold mb-1${isShiny ? ' golden-text' : ''}">
                                            ${p.display_name || 'Nombre Desconocido'}
                                        </h3>
                                        <div class="mb-2">
                                            ${(p.types || []).map(t => {
                                                const key = (t || '').toLowerCase();
                                                const label = typeTranslations[key] || (key ? key.charAt(0).toUpperCase() + key.slice(1) : 'Tipo Desconocido');
                                                return `<span class="type-badge ${key} mx-1">${label}</span>`;
                                            }).join('')}
                                        </div>
                                        <a href="/pokemon/${p.id || '#'}" class="btn btn-primary mt-2">Ver detalles</a>
                                    </div>
                                </div>`;
                            });

                            result.innerHTML = `<div class="random-pokemon-row">${cardsHtml}</div>`;
                            setTimeout(() => {
                                const container = result.querySelector('.random-pokemon-row');
                                if (container) {
                                    container.style.opacity = '1';
                                    container.style.transform = 'scale(1)';
                                }
                                result.classList.add('random');

                                // --- REINICIAR ANIMACIÓN BOUNCE EN HOVER ---
                                document.querySelectorAll('.random-pokemon-card img.pokemon-image').forEach(img => {
                                    img.addEventListener('mouseenter', function () {
                                        this.style.animation = 'none';
                                        // Forzar reflow
                                        void this.offsetWidth;
                                        this.style.animation = '';
                                    });
                                });
                            }, 50);
                        } else {
                            result.innerHTML = `<div class="alert alert-warning text-center">No se encontró ningún Pokémon con esos filtros o la API no devolvió datos válidos.</div>`;
                            result.classList.remove('random');
                        }
                    })
                    .catch((error) => {
                        loader.classList.add('d-none');
                        console.error("Error en la solicitud fetch de Pokémon aleatorio:", error);
                        result.innerHTML = `<div class="alert alert-danger text-center">Error al buscar Pokémon aleatorio. Por favor, inténtalo de nuevo. (Revisa la consola para más detalles)</div>`;
                        result.classList.remove('random');
                    });
            }

            btn.addEventListener('click', fetchRandomPokemon);
        });
    </script>
@endpush

@section('content')
    <div class="min-vh-100">
        <div class="header-section text-center mb-4">
            <h1 class="display-5 fw-bold">Pokémon Aleatorio</h1>
            <p class="text-light">¿No sabes qué Pokémon ver? ¡Déjalo al azar! Puedes filtrar por tipo, grupo huevo y
                cantidad.</p>
        </div>

        <div class="row justify-content-center mb-4">
            <div class="col-12 col-md-3 mb-2 mb-md-0">
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
            <div class="col-12 col-md-3 mb-2 mb-md-0">
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
            <div class="col-12 col-md-2 mb-2 mb-md-0">
                <select class="form-select" id="qtySelect">
                    @for ($i = 1; $i <= 6; $i++)
                        <option value="{{ $i }}" {{ $i == 6 ? 'selected' : '' }}>{{ $i }} Pokémon</option>
                    @endfor
                </select>
            </div>
            <div class="col-12 col-md-2 d-flex align-items-center justify-content-center">
                <button id="randomBtn" class="btn btn-primary w-100">
                    <i class="bi bi-shuffle"></i> ¡Generar!
                </button>
            </div>
        </div>

        <div class="d-flex justify-content-center mb-3">
            <div id="randomLoader" class="d-none">
                <div class="spinner-border text-golden-bloom" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
            </div>
        </div>

        <div id="randomResult" class="mt-3"></div>
    </div>
@endsection