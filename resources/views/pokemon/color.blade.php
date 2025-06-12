@extends('layouts.app')

@section('title', config('app.name') . ' - Pokémon Aleatorio con Paleta de Colores')

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/color-thief/2.3.2/color-thief.umd.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const btn = document.getElementById('randomBtn');
            const shinyBtn = document.getElementById('shinyBtn');
            const result = document.getElementById('randomResult');
            const loader = document.getElementById('randomLoader');
            const pokemonNameElement = document.getElementById('pokemon-name');
            const paletteDisplay = document.getElementById('palette');
            const pokemonImage = document.getElementById('pokemonImage');
            const colorThief = new ColorThief();

            // Elementos para los detalles del Pokémon
            const pokemonIdElement = document.getElementById('pokemon-id');
            const pokemonTypesElement = document.getElementById('pokemon-types');

            // Variables globales para los colores y shiny
            let currentPalette = [];
            let currentCopyTooltip = null;
            let isShiny = false;
            let currentPokemon = null;

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
                    return lightness > 30 && lightness < 225;
                });

                usableColors.sort((a, b) => {
                    const [hA, sA, lA] = rgbToHsl(...a);
                    const [hB, sB, lB] = rgbToHsl(...b);
                    const scoreA = sA * (1 - Math.abs(lA - 0.5));
                    const scoreB = sB * (1 - Math.abs(lB - 0.5));
                    return scoreB - scoreA;
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

                while (symbolicColors.length < numColors) {
                    symbolicColors.push([...symbolicColors[0]]);
                }

                return symbolicColors;
            }

            // Función para aplicar colores a todos los elementos
            function applyColorTheme(symbolicColors) {
                currentPalette = symbolicColors;

                const cards = document.querySelectorAll('.pokemon-card');
                cards.forEach((card, index) => {
                    const colorIndex = index % symbolicColors.length;
                    const rgb = `rgb(${symbolicColors[colorIndex].join(',')})`;
                    const [h, s, l] = rgbToHsl(...symbolicColors[colorIndex]);

                    // Usar el tercer color (índice 2) para el glow de fondo, o el primer color si no hay suficientes
                    const glowColorIndex = symbolicColors.length > 2 ? 2 : 0;
                    const glowRgb = `rgb(${symbolicColors[glowColorIndex].join(',')})`;
                    
                    card.style.setProperty('--card-color', rgb);
                    card.style.setProperty('--card-color-light', `rgba(${symbolicColors[colorIndex].join(',')}, 0.1)`);
                    card.style.setProperty('--card-color-medium', `rgba(${symbolicColors[colorIndex].join(',')}, 0.3)`);
                    card.style.setProperty('--text-color', l > 0.6 ? '#121621' : '#ffffff');
                    card.style.setProperty('--glow-color', glowRgb);

                    const cardTitle = card.querySelector('.card-title');
                    if (cardTitle) {
                        cardTitle.style.color = rgb;
                    }
                });

                // Aplicar colores a los botones con !important para sobrescribir CSS
                const primaryColor = `rgb(${symbolicColors[0].join(',')})`;
                const [h1, s1, l1] = rgbToHsl(...symbolicColors[0]);
                const primaryTextColor = l1 > 0.6 ? "#121621" : "#fff";
                
                // Crear gradiente para el botón principal
                const primaryGradient = `linear-gradient(135deg, ${primaryColor}, rgba(${symbolicColors[0].join(',')}, 0.8))`;
                const primaryShadow = `0 8px 25px rgba(${symbolicColors[0].join(',')}, 0.3)`;
                const primaryHoverShadow = `0 12px 35px rgba(${symbolicColors[0].join(',')}, 0.4)`;
                
                btn.style.setProperty('background', primaryGradient, 'important');
                btn.style.setProperty('color', primaryTextColor, 'important');
                btn.style.setProperty('box-shadow', primaryShadow, 'important');
                btn.setAttribute('data-hover-shadow', primaryHoverShadow);

                // Aplicar color al botón shiny
                if (shinyBtn) {
                    const secondaryColor = symbolicColors.length > 1 ? `rgb(${symbolicColors[1].join(',')})` : primaryColor;
                    const [h2, s2, l2] = symbolicColors.length > 1 ? rgbToHsl(...symbolicColors[1]) : [h1, s1, l1];
                    const secondaryTextColor = l2 > 0.6 ? "#121621" : "#fff";
                    
                    // Crear gradiente para el botón shiny
                    const secondaryColorArray = symbolicColors.length > 1 ? symbolicColors[1] : symbolicColors[0];
                    const secondaryGradient = `linear-gradient(135deg, ${secondaryColor}, rgba(${secondaryColorArray.join(',')}, 0.8))`;
                    const secondaryShadow = `0 8px 25px rgba(${secondaryColorArray.join(',')}, 0.3)`;
                    const secondaryHoverShadow = `0 12px 35px rgba(${secondaryColorArray.join(',')}, 0.4)`;
                    
                    shinyBtn.style.setProperty('background', secondaryGradient, 'important');
                    shinyBtn.style.setProperty('color', secondaryTextColor, 'important');
                    shinyBtn.style.setProperty('box-shadow', secondaryShadow, 'important');
                    shinyBtn.setAttribute('data-hover-shadow', secondaryHoverShadow);
                }

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

                    box.addEventListener('click', function (event) {
                        event.stopPropagation();
                        showCopyOptions(this, rgbString, hexString);
                    });

                    paletteDisplay.appendChild(box);
                });
                paletteDisplay.classList.add('loaded');
            }

            // Función para mostrar opciones de copiar color
            function showCopyOptions(element, rgb, hex) {
                if (currentCopyTooltip) {
                    currentCopyTooltip.remove();
                    currentCopyTooltip = null;
                }

                const clickedColor = rgb;
                const rgbValues = rgb.match(/\d+/g).map(Number);
                const [r, g, b] = rgbValues;
                const luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;
                const textColor = luminance > 0.6 ? '#121621' : '#ffffff';

                const tooltip = document.createElement('div');
                tooltip.className = 'color-copy-tooltip';
                
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

                tooltip.querySelectorAll('.copy-btn').forEach(button => {
                    button.addEventListener('click', async function (e) {
                        e.stopPropagation();
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
                }, 100);
            }

            // Función para obtener URL de imagen shiny/normal
            function getImageUrl(pokemon, shiny = false) {
                if (!pokemon.image) return null;
                
                const baseUrl = pokemon.image;
                if (shiny) {
                    if (baseUrl.includes('/official-artwork/')) {
                        return baseUrl.replace('/official-artwork/', '/official-artwork/shiny/');
                    }
                    return baseUrl;
                } else {
                    if (baseUrl.includes('/shiny/')) {
                        return baseUrl.replace('/official-artwork/shiny/', '/official-artwork/');
                    }
                    return baseUrl;
                }
            }

            // Función para actualizar los detalles del Pokémon en la UI
            function updatePokemonDetails(pokemon, forceShiny = null) {
                const useShiny = forceShiny !== null ? forceShiny : isShiny;
                
                pokemonNameElement.textContent = (pokemon.display_name || pokemon.name || 'Desconocido').toUpperCase();
                pokemonIdElement.textContent = `#${pokemon.pokeapi_id || 'N/A'}`;
                
                const imageUrl = getImageUrl(pokemon, useShiny);
                pokemonImage.src = imageUrl || '{{ asset('images/pokemon/placeholder.png') }}';

                if (Array.isArray(pokemon.types) && pokemon.types.length > 0) {
                    pokemonTypesElement.innerHTML = pokemon.types.map(type => {
                        const typeInLowerCase = type.toLowerCase();
                        const translatedType = typeTranslations[typeInLowerCase] || type;
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

            // Función para extraer colores y aplicar tema
            async function extractAndApplyColors(imageUrl) {
                try {
                    const img = await preloadImage(imageUrl);
                    const palette = colorThief.getPalette(img, 8);
                    const symbolicColors = getSymbolicColors(palette, 5);
                    applyColorTheme(symbolicColors);
                } catch (error) {
                    console.error('Error extracting colors:', error);
                }
            }

            // Función para alternar entre shiny y normal
            async function toggleShiny() {
                if (!currentPokemon) return;

                isShiny = !isShiny;
                
                // Actualizar texto del botón
                shinyBtn.innerHTML = isShiny 
                    ? '<i class="bi bi-star-fill"></i> Normal' 
                    : '<i class="bi bi-star"></i> Shiny';

                // Animación de transición
                pokemonImage.style.transition = 'opacity 0.25s';
                pokemonImage.style.opacity = '0';

                setTimeout(async () => {
                    // Actualizar imagen
                    const newImageUrl = getImageUrl(currentPokemon, isShiny);
                    pokemonImage.src = newImageUrl;
                    
                    // Extraer nuevos colores
                    await extractAndApplyColors(newImageUrl);
                    
                    pokemonImage.style.opacity = '1';
                }, 250);
            }

            // Función principal para obtener Pokémon aleatorio
            async function fetchRandomPokemon() {
                if (currentCopyTooltip) {
                    currentCopyTooltip.remove();
                    currentCopyTooltip = null;
                }

                result.innerHTML = '';
                loader.classList.remove('d-none');
                btn.disabled = true;
                btn.textContent = 'Generando...';
                
                if (shinyBtn) {
                    shinyBtn.disabled = true;
                }

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
                    currentPokemon = pokemon;
                    
                    isShiny = false;
                    if (shinyBtn) {
                        shinyBtn.innerHTML = '<i class="bi bi-star"></i> Shiny';
                    }

                    const imageUrl = getImageUrl(pokemon, false);

                    if (!imageUrl) {
                        throw new Error('No se encontró imagen para este Pokémon.');
                    }

                    document.querySelectorAll('.pokemon-card').forEach(card => {
                        card.classList.add('fade-out');
                    });

                    await new Promise(resolve => setTimeout(resolve, 300));

                    updatePokemonDetails(pokemon, false);
                    await extractAndApplyColors(imageUrl);

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
                    
                    if (shinyBtn) {
                        shinyBtn.disabled = false;
                    }
                }
            }

            // Agregar event listeners para efectos hover dinámicos
            function addHoverEffects() {
                [btn, shinyBtn].forEach(button => {
                    if (button) {
                        button.addEventListener('mouseenter', function() {
                            const hoverShadow = this.getAttribute('data-hover-shadow');
                            if (hoverShadow) {
                                this.style.setProperty('box-shadow', hoverShadow, 'important');
                            }
                        });
                        
                        button.addEventListener('mouseleave', function() {
                            // Restaurar sombra original basada en los colores actuales
                            if (currentPalette.length > 0) {
                                const isShinyButton = this === shinyBtn;
                                const colorIndex = isShinyButton && currentPalette.length > 1 ? 1 : 0;
                                const originalShadow = `0 8px 25px rgba(${currentPalette[colorIndex].join(',')}, 0.3)`;
                                this.style.setProperty('box-shadow', originalShadow, 'important');
                            }
                        });
                    }
                });
            }

            // Configurar eventos de los botones
            btn.addEventListener('click', fetchRandomPokemon);
            if (shinyBtn) {
                shinyBtn.addEventListener('click', toggleShiny);
            }

            // Agregar efectos hover
            addHoverEffects();

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
            <button id="shinyBtn" style="margin-left: 10px;">
                <i class="bi bi-star"></i>
                Shiny
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
                        onerror="this.onerror=null;this.src='{{ asset('images/default-user.png') }}';">
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

        /* Estilos base para los botones (sin background fijo) */
        #randomBtn, #shinyBtn {
            border: none;
            padding: 1rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            /* Removido background fijo para permitir colores dinámicos */
        }

        #randomBtn:hover, #shinyBtn:hover {
            transform: translateY(-2px);
            /* box-shadow se maneja dinámicamente en JavaScript */
        }

        #randomBtn:disabled, #shinyBtn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
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

        .pokemon-card::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 150%;
            height: 150%;
            transform: translate(-50%, -50%);
            background: radial-gradient(circle, var(--glow-color, rgba(102, 126, 234, 0.2)) 0%, transparent 70%);
            opacity: 0.08;
            z-index: -1;
            pointer-events: none;
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
            position: relative;
            overflow: hidden;
        }

        .main-card::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 150%;
            height: 150%;
            transform: translate(-50%, -50%);
            background: radial-gradient(circle, var(--glow-color, rgba(102, 126, 234, 0.2)) 0%, transparent 70%);
            opacity: 0.08;
            z-index: -1;
            pointer-events: none;
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
            #shinyBtn {
                margin-left: 0 !important;
                margin-top: 10px;
            }
        }
    </style>
@endpush