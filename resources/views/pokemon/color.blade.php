@extends('layouts.app')

@section('title', config('app.name') . ' - Pokémon Aleatorio con Paleta de Colores')

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/color-thief/2.3.2/color-thief.umd.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const btn = document.getElementById('randomBtn');
            const result = document.getElementById('randomResult');
            const loader = document.getElementById('randomLoader');
            const pokemonNameElement = document.getElementById('pokemon-name');
            const paletteDisplay = document.getElementById('palette');
            const pokemonImage = document.getElementById('pokemonImage');
            const colorThief = new ColorThief();

            // Elementos para los detalles del Pokémon
            const pokemonIdElement = document.getElementById('pokemon-id');
            const pokemonTypesElement = document.getElementById('pokemon-types');

            // Variables globales para los colores
            let currentPalette = [];
            let currentCopyTooltip = null; // Para manejar el tooltip de copiar color

            // Traducciones de los tipos de Pokémon
            const typeTranslations = {
                normal: 'Normal',
                fire: 'Fuego',
                water: 'Agua',
                electric: 'Eléctrico',
                grass: 'Planta',
                ice: 'Hielo',
                fighting: 'Lucha',
                poison: 'Veneno',
                ground: 'Tierra',
                flying: 'Volador',
                psychic: 'Psíquico',
                bug: 'Bicho',
                rock: 'Roca',
                ghost: 'Fantasma',
                dragon: 'Dragón',
                dark: 'Siniestro',
                steel: 'Acero',
                fairy: 'Hada'
            };

            // Función para convertir RGB a HSL
            function rgbToHsl(r, g, b) {
                r /= 255; g /= 255; b /= 255;
                const max = Math.max(r, g, b), min = Math.min(r, g, b);
                let h, s, l = (max + min) / 2;
                if (max === min) { h = s = 0; } else {
                    const d = max - min;
                    s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
                    switch (max) {
                        case r: h = (g - b) / d + (g < b ? 6 : 0); break;
                        case g: h = (b - r) / d + 2; break;
                        case b: h = (r - g) / d + 4; break;
                    }
                    h /= 6;
                }
                return [h * 360, s, l];
            }

            // Función para convertir RGB a Hex
            function rgbToHex(r, g, b) {
                return "#" + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1).toUpperCase();
            }

            // Función para calcular distancia entre colores
            function colorDistance(c1, c2) {
                return Math.sqrt(
                    Math.pow(c1[0] - c2[0], 2) +
                    Math.pow(c1[1] - c2[1], 2) +
                    Math.pow(c1[2] - c2[2], 2)
                );
            }

            // Función para obtener colores simbólicos distintos
            function getSymbolicColors(colors, numColors = 5) {
                const symbolicColors = [];
                const minDistance = 60;

                const usableColors = colors.filter(([r, g, b]) => {
                    const lightness = (Math.max(r, g, b) + Math.min(r, g, b)) / 2;
                    return lightness > 30 && lightness < 225; // Evitar colores muy claros o muy oscuros
                });

                usableColors.sort((a, b) => {
                    const [hA, sA, lA] = rgbToHsl(...a);
                    const [hB, sB, lB] = rgbToHsl(...b);
                    const scoreA = sA * (1 - Math.abs(lA - 0.5));
                    const scoreB = sB * (1 - Math.abs(lB - 0.5));
                    return scoreB - scoreA; // Priorizar colores más saturados y de luminosidad media
                });

                if (usableColors.length > 0) {
                    symbolicColors.push(usableColors[0]);
                }

                for (let i = 1; i < usableColors.length && symbolicColors.length < numColors; i++) {
                    let isDistinct = true;
                    for (const sc of symbolicColors) {
                        if (colorDistance(sc, usableColors[i]) < minDistance) {
                            isDistinct = false;
                            break;
                        }
                    }
                    if (isDistinct) {
                        symbolicColors.push(usableColors[i]);
                    }
                }

                // Rellenar si no hay suficientes colores distintos
                while (symbolicColors.length < numColors) {
                    symbolicColors.push([...symbolicColors[0]]); // Usar el primer color para rellenar
                }

                return symbolicColors;
            }

            // Función para aplicar colores a todos los elementos
            function applyColorTheme(symbolicColors) {
                currentPalette = symbolicColors;

                // Aplicar colores a las cards
                const cards = document.querySelectorAll('.pokemon-card');
                cards.forEach((card, index) => {
                    const colorIndex = index % symbolicColors.length;
                    const rgb = `rgb(${symbolicColors[colorIndex].join(',')})`;
                    const [h, s, l] = rgbToHsl(...symbolicColors[colorIndex]);

                    card.style.setProperty('--card-color', rgb);
                    card.style.setProperty('--card-color-light', `rgba(${symbolicColors[colorIndex].join(',')}, 0.1)`);
                    card.style.setProperty('--card-color-medium', `rgba(${symbolicColors[colorIndex].join(',')}, 0.3)`);
                    card.style.setProperty('--text-color', l > 0.6 ? '#121621' : '#ffffff');

                    // Aplicar color a los títulos de las cards
                    const cardTitle = card.querySelector('.card-title');
                    if (cardTitle) {
                        cardTitle.style.color = rgb;
                    }
                });

                // Aplicar color principal al botón
                const primaryColor = `rgb(${symbolicColors[0].join(',')})`;
                btn.style.backgroundColor = primaryColor;
                const [h, s, l] = rgbToHsl(...symbolicColors[0]);
                btn.style.color = l > 0.6 ? "#121621" : "#fff";

                // Actualizar paleta visual
                updatePaletteDisplay(symbolicColors);
            }

            // Función para actualizar la paleta visual y añadir funcionalidad de copiar
            function updatePaletteDisplay(symbolicColors) {
                paletteDisplay.innerHTML = '';
                symbolicColors.forEach((color, index) => {
                    const rgbString = `rgb(${color.join(',')})`;
                    const hexString = rgbToHex(...color);

                    const box = document.createElement('div');
                    box.className = 'color-box';
                    box.style.backgroundColor = rgbString;
                    box.style.animationDelay = `${index * 0.1}s`;
                    box.setAttribute('data-rgb', rgbString);
                    box.setAttribute('data-hex', hexString);

                    // Añadir evento click para copiar color
                    box.addEventListener('click', function (event) {
                        event.stopPropagation(); // Evitar que el clic se propague al documento
                        showCopyOptions(this, rgbString, hexString);
                    });

                    paletteDisplay.appendChild(box);
                });
                paletteDisplay.classList.add('loaded');
            }

            // Función para mostrar opciones de copiar color (CORREGIDA)
            function showCopyOptions(element, rgb, hex) {
                // Eliminar cualquier tooltip existente
                if (currentCopyTooltip) {
                    currentCopyTooltip.remove();
                    currentCopyTooltip = null;
                }

                // Usar el color específico del elemento clickeado
                const clickedColor = rgb;
                const rgbValues = rgb.match(/\d+/g).map(Number);
                const [r, g, b] = rgbValues;
                const luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;
                const textColor = luminance > 0.6 ? '#121621' : '#ffffff';

                const tooltip = document.createElement('div');
                tooltip.className = 'color-copy-tooltip';
                
                // Estructura HTML usando el color específico clickeado
                tooltip.innerHTML = `
                    <h5 class="tooltip-title">Copiar Formato</h5>
                    <button class="copy-btn" data-color="${hex}" style="background-color: ${clickedColor}; color: ${textColor}; border-color: ${clickedColor};">
                        <span class="copy-text">HEX | ${hex}</span>
                    </button>
                    <button class="copy-btn" data-color="${rgb}" style="background-color: ${clickedColor}; color: ${textColor}; border-color: ${clickedColor};">
                        <span class="copy-text">RGB | ${rgb.replace('rgb', '')}</span>
                    </button>
                `;

                document.body.appendChild(tooltip);
                currentCopyTooltip = tooltip;

                const rect = element.getBoundingClientRect();
                tooltip.style.top = `${rect.bottom + window.scrollY + 10}px`;
                tooltip.style.left = `${rect.left + window.scrollX + (rect.width / 2)}px`;

                // Añadir event listeners a los botones de copiar (CORREGIDO)
                tooltip.querySelectorAll('.copy-btn').forEach(button => {
                    button.addEventListener('click', async function (e) {
                        e.stopPropagation(); // Evitar propagación
                        const colorToCopy = this.dataset.color;
                        const originalHTML = this.innerHTML;

                        try {
                            await navigator.clipboard.writeText(colorToCopy);
                            this.classList.add('copied');
                            this.style.backgroundColor = '#5a9965'; 
                            this.style.borderColor = '#5a9965';
                            this.style.color = '#ffffff';
                            this.innerHTML = `
                                <span class="copy-text">¡Copiado!</span>
                                <i class="bi bi-check-lg"></i>
                            `;
                            
                            setTimeout(() => {
                                this.classList.remove('copied');
                                this.style.backgroundColor = clickedColor;
                                this.style.borderColor = clickedColor;
                                this.style.color = textColor;
                                this.innerHTML = originalHTML;
                            }, 1200);
                            
                            // Cerrar tooltip después de copiar
                            setTimeout(() => {
                                if (currentCopyTooltip) {
                                    currentCopyTooltip.remove();
                                    currentCopyTooltip = null;
                                }
                            }, 1500);
                            
                        } catch (err) {
                            console.error('Error al copiar el color:', err);
                            this.innerHTML = 'Error al copiar';
                            setTimeout(() => {
                                this.innerHTML = originalHTML;
                            }, 1500);
                        }
                    });
                });

                setTimeout(() => {
                    const handleClickOutside = (e) => {
                        if (currentCopyTooltip && 
                            !currentCopyTooltip.contains(e.target) && 
                            !element.contains(e.target)) {
                            currentCopyTooltip.remove();
                            currentCopyTooltip = null;
                            document.removeEventListener('click', handleClickOutside);
                        }
                    };
                    document.addEventListener('click', handleClickOutside);
                }, 100); // Pequeño delay para evitar cierre inmediato
            }

            // Función para actualizar los detalles del Pokémon en la UI
            function updatePokemonDetails(pokemon) {
                // Nombre e ID
                pokemonNameElement.textContent = (pokemon.display_name || pokemon.name || 'Desconocido').toUpperCase();
                pokemonIdElement.textContent = `#${pokemon.pokeapi_id || 'N/A'}`;
                pokemonImage.src = pokemon.image || '{{ asset('images/pokemon/placeholder.png') }}';

                // Tipos - ahora se mostrarán dentro del contenedor de la imagen y traducidos
                if (Array.isArray(pokemon.types) && pokemon.types.length > 0) {
                    pokemonTypesElement.innerHTML = pokemon.types.map(type => {
                        const typeInLowerCase = type.toLowerCase();
                        const translatedType = typeTranslations[typeInLowerCase] || type; // Usar traducción o el original
                        return `<span class="type-badge type-${typeInLowerCase}">${translatedType.toUpperCase()}</span>`;
                    }).join('');
                } else {
                    pokemonTypesElement.innerHTML = '<span class="text-muted">Sin tipos</span>';
                }
            }

            // Función para precargar imagen
            function preloadImage(src) {
                return new Promise((resolve, reject) => {
                    const newImg = new Image();
                    newImg.crossOrigin = "anonymous";
                    newImg.src = src;
                    newImg.onload = () => resolve(newImg);
                    newImg.onerror = reject;
                });
            }

            // Función principal para obtener Pokémon aleatorio
            async function fetchRandomPokemon() {
                // Eliminar tooltip de copiar si existe
                if (currentCopyTooltip) {
                    currentCopyTooltip.remove();
                    currentCopyTooltip = null;
                }

                result.innerHTML = '';
                loader.classList.remove('d-none');
                btn.disabled = true;
                btn.textContent = 'Generando...';

                try {
                    const type = document.getElementById('typeSelect')?.value || 'all';
                    const eggGroup = document.getElementById('eggGroupSelect')?.value || 'all';

                    const response = await fetch(`/api/random-pokemon?type=${type}&egg_group=${eggGroup}`);

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const data = await response.json();

                    if (!data.success || !data.data) {
                        throw new Error('No se pudo obtener datos del Pokémon');
                    }

                    const pokemon = data.data;
                    const imageUrl = pokemon.image;

                    if (!imageUrl) {
                        throw new Error('No se encontró imagen para este Pokémon.');
                    }

                    // Animación de salida
                    document.querySelectorAll('.pokemon-card').forEach(card => {
                        card.classList.add('fade-out');
                    });

                    await new Promise(resolve => setTimeout(resolve, 300)); // Esperar a que la animación de salida termine

                    // Actualizar datos
                    updatePokemonDetails(pokemon);

                    // Cargar imagen y extraer colores
                    const img = await preloadImage(imageUrl);
                    const palette = colorThief.getPalette(img, 8);
                    const symbolicColors = getSymbolicColors(palette, 5);

                    // Aplicar tema de colores
                    applyColorTheme(symbolicColors);

                    // Animación de entrada
                    document.querySelectorAll('.pokemon-card').forEach((card, index) => {
                        card.classList.remove('fade-out');
                        card.classList.add('fade-in');
                        card.style.animationDelay = `${index * 0.1}s`;
                    });

                } catch (error) {
                    console.error("Error:", error);
                    result.innerHTML = `<div class="alert alert-danger">Error: ${error.message}</div>`;
                } finally {
                    loader.classList.add('d-none');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-shuffle"></i> ¡Generar Pokémon!';
                }
            }

            // Configurar evento del botón
            btn.addEventListener('click', fetchRandomPokemon);

            // Cargar un Pokémon aleatorio al cargar la página
            fetchRandomPokemon();
        });
    </script>
@endpush

@section('content')
    <div class="header-section text-center mb-4">
        <h1 class="display-5 fw-bold">Generador de paletas</h1>
        <p class="text-light">¿Quieres una paleta de colores? ¡Inspírate en un Pokémon!</p>
    </div>
    <div class="container pokemon-dashboard py-5">

        <div class="controls text-center mb-5">
            <button id="randomBtn">
                <i class="bi bi-shuffle"></i>
                ¡Generar Pokémon!
            </button>
            <div id="randomLoader" class="loader d-none mt-3">
                <div class="spinner"></div>
            </div>
        </div>

        <div class="pokemon-grid">
            <div class="pokemon-card main-card">
                <div class="card-header">
                    <h2 id="pokemon-name" class="pokemon-name">???</h2>
                    <span id="pokemon-id" class="pokemon-id">#???</span>
                </div>
                <div class="pokemon-image-container">
                    <img id="pokemonImage" crossorigin="anonymous" class="pokemon-image img-fluid" alt="Pokémon"
                        onerror="this.onerror=null;this.src='{{ asset('images/pokemon/placeholder.png') }}';">
                    <div id="pokemon-types" class="types-container-overlay"></div>
                </div>
            </div>

            <div class="pokemon-card palette-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="bi bi-clipboard"></i> Paleta de Colores
                    </h3>
                </div>
                <div id="palette" class="color-palette"></div>
            </div>
        </div>

        <div id="randomResult" class="mt-4"></div>
    </div>
@endsection

@push('styles')
    <style>
        :root {
            --primary-bg: #0a0e1a;
            --secondary-bg: #1a1f2e;
            --card-bg: #252b3d;
            --text-primary: #ffffff;
            --text-secondary: #b8c5d6;
            --border-color: #2d3748;
            --shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            --border-radius: 16px;
            --success-color: #4ade80;
        }

        body {
            color: var(--text-primary);
            font-family: 'Josefin Sans', sans-serif;
        }

        .pokemon-dashboard {
            margin: 0 auto;
            max-width: 1400px;
        }

        /* Controles */
        .controls {
            margin-bottom: 3rem;
        }

        .loader {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 50px;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 3px solid rgba(255, 255, 255, 0.1);
            border-top: 3px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Grid principal con Flexbox */
        .pokemon-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .pokemon-card {
            background: var(--card-bg);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            box-shadow: var(--shadow);
            border: 1px solid var(--border-color);
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
            flex: 1 1 300px;
            max-width: 100%;
            min-width: 250px;
        }
        
        .main-card {
            flex: 2 1 500px;
        }

        .pokemon-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--card-color, linear-gradient(135deg, #667eea 0%, #764ba2 100%));
            opacity: 0.8;
        }

        .pokemon-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        }

        .main-card {
            text-align: center;
            background: linear-gradient(135deg, var(--card-color-light, rgba(102, 126, 234, 0.1)) 0%, var(--card-bg) 100%);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid var(--border-color);
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .pokemon-name {
            font-size: 2rem;
            font-weight: 800;
            margin: 0;
            color: var(--card-color, var(--text-primary));
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .pokemon-id {
            font-size: 1.2rem;
            color: var(--card-color, var(--text-secondary));
            font-weight: 600;
            opacity: 0.8;
        }

        .pokemon-image-container {
            margin: 1.5rem 0;
            position: relative;
            min-height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .pokemon-image {
            max-width: 100%;
            height: auto;
            max-height: 250px;
            border-radius: 12px;
            filter: drop-shadow(0 10px 20px rgba(0, 0, 0, 0.3));
            transition: all 0.4s ease;
            object-fit: contain;
        }

        .pokemon-image:hover {
            transform: scale(1.05);
        }
        
        .types-container-overlay {
            position: absolute;
            bottom: 10px;
            left: 10px;
            right: 10px;
            display: flex;
            justify-content: flex-end;
            gap: 0.5rem;
            z-index: 10;
        }

        .color-palette {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(60px, 1fr));
            gap: 1rem;
            opacity: 0;
            transition: opacity 0.6s ease;
            padding-top: 1rem;
        }

        .color-palette.loaded {
            opacity: 1;
        }

        .color-box {
            aspect-ratio: 1;
            border-radius: 12px;
            position: relative;
            cursor: pointer;
            transition: all 0.3s ease;
            animation: colorBoxAppear 0.6s ease forwards;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .color-box:hover {
            transform: scale(1.1);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
        }

        /* Estilos actualizados para el tooltip */
        .color-copy-tooltip {
            position: absolute;
            background-color: rgba(10, 14, 26, 0.98);
            backdrop-filter: blur(8px);
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            padding: 1rem;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.6);
            z-index: 1000;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            min-width: 220px;
            transform: translateX(-50%);
            animation: fadeInTooltip 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
        }

        .tooltip-title {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0 0 0.25rem 0;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .copy-btn {
            padding: 0.6rem 1rem;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.2s ease;
            white-space: nowrap;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid;
        }

        .copy-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        .copy-btn.copied {
            transform: scale(1.02);
        }

        @keyframes fadeInTooltip {
            from { 
                opacity: 0; 
                transform: translateX(-50%) translateY(10px) scale(0.95); 
            }
            to { 
                opacity: 1; 
                transform: translateX(-50%) translateY(0) scale(1); 
            }
        }

        @keyframes colorBoxAppear {
            0% {
                opacity: 0;
                transform: scale(0.8) rotateY(90deg);
            }

            100% {
                opacity: 1;
                transform: scale(1) rotateY(0deg);
            }
        }

        .type-badge {
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            background: var(--card-color-medium, rgba(102, 126, 234, 0.3));
            color: var(--text-color, var(--text-primary));
            border-color: var(--card-color, #667eea);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }

        /* Animaciones */
        .fade-out { animation: fadeOut 0.3s ease forwards; }
        .fade-in { animation: fadeIn 0.6s ease forwards; }

        @keyframes fadeOut { to { opacity: 0; transform: translateY(20px) scale(0.95); } }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px) scale(0.95); } to { opacity: 1; transform: translateY(0) scale(1); } }

        /* Type-specific colors */
        .type-normal { background: linear-gradient(135deg, #a8a878, #8c8c5c) !important; color: #222 !important; }
        .type-fuego, .type-fire { background: linear-gradient(135deg, #ef8030, #cc6600) !important; color: #fff !important; }
        .type-agua, .type-water { background: linear-gradient(135deg, #6890f0, #3860d0) !important; color: #fff !important; }
        .type-eléctrico, .type-electric { background: linear-gradient(135deg, #f9d030, #e6b800) !important; color: #222 !important; }
        .type-planta, .type-grass { background: linear-gradient(135deg, #78c84f, #5a9c3c) !important; color: #222 !important; }
        .type-hielo, .type-ice { background: linear-gradient(135deg, #99d8d8, #66bcbc) !important; color: #222 !important; }
        .type-lucha, .type-fighting { background: linear-gradient(135deg, #c03128, #8c1f18) !important; color: #fff !important; }
        .type-veneno, .type-poison { background: linear-gradient(135deg, #a040a0, #803080) !important; color: #fff !important; }
        .type-tierra, .type-ground { background: linear-gradient(135deg, #e0c068, #c0a050) !important; color: #222 !important; }
        .type-volador, .type-flying { background: linear-gradient(135deg, #a790f0, #8c78d8) !important; color: #222 !important; }
        .type-psíquico, .type-psychic { background: linear-gradient(135deg, #f85888, #e04070) !important; color: #fff !important; }
        .type-bicho, .type-bug { background: linear-gradient(135deg, #a8b820, #8c9c1c) !important; color: #222 !important; }
        .type-roca, .type-rock { background: linear-gradient(135deg, #b7a039, #95822e) !important; color: #fff !important; }
        .type-fantasma, .type-ghost { background: linear-gradient(135deg, #705898, #5a4378) !important; color: #fff !important; }
        .type-dragón, .type-dragon { background: linear-gradient(135deg, #7038f8, #5020d0) !important; color: #fff !important; }
        .type-siniestro, .type-dark { background: linear-gradient(135deg, #6f5848, #4c392f) !important; color: #fff !important; }
        .type-acero, .type-steel { background: linear-gradient(135deg, #b8b8d0, #9898b0) !important; color: #222 !important; }
        .type-hada, .type-fairy { background: linear-gradient(135deg, #f0b6bc, #e19098) !important; color: #222 !important; }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .pokemon-grid {
                flex-direction: column;
            }
            .pokemon-card {
                flex: 1 1 auto;
                max-width: 100%;
            }
            .header-section h1 {
                font-size: 2.2rem;
            }
            .header-section p {
                font-size: 1rem;
            }
            .types-container-overlay {
                justify-content: center;
                bottom: 5px;
            }
        }
    </style>
@endpush