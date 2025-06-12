@extends('layouts.app')

@section('title', 'Poketek - Inicio')

@section('content')
    <section class="header-section text-center py-5">
        <div class="container">
            <h1 class="display-4">¡Bienvenidos a Pokétek!</h1>
            <p class="description-card">Tu pokédex personal e interactiva.</p>
        </div>
    </section>

    <section id="features" class="container py-5">
        <div class="row justify-content-center g-4">
            {{-- Card: PokéDex --}}
            <div class="col-md-6 col-lg-4 d-flex">
                <a href="{{ route('pokedex.index') }}" class="bento-card card-link text-center w-100">
                    <div class="card-content-wrapper">
                        <div class="pokemon-image-container">
                            <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/479.png" alt="Rotom" class="pokemon-card-img">
                        </div>
                        <div class="divider"></div>
                        <h3 class="card-title mt-3">Pokédex</h3>
                        <p class="description-card">Descubre una extensa base de datos con detalles de cada Pokémon, incluyendo tipos, habilidades y estadísticas.</p>
                    </div>
                </a>
            </div>

            {{-- Card: Generar Pokémon Aleatorio --}}
            <div class="col-md-6 col-lg-4 d-flex">
                <a href="{{ route('pokemon.random') }}" class="bento-card card-link text-center w-100">
                    <div class="card-content-wrapper">
                        <div class="pokemon-image-container">
                            <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/778.png" alt="Mimikyu" class="pokemon-card-img">
                        </div>
                        <div class="divider"></div>
                        <h3 class="card-title mt-3">Generar Pokémon</h3>
                        <p class="description-card">Encuentra un Pokémon aleatorio y sorpréndete con sus datos, habilidades y estadísticas, ¡perfecto para una aventura!</p>
                    </div>
                </a>
            </div>

            {{-- Card: Colores de Pokémon --}}
            <div class="col-md-6 col-lg-4 d-flex">
                <a href="{{ route('pokemon.color') }}" class="bento-card card-link text-center w-100">
                    <div class="card-content-wrapper">
                        <div class="pokemon-image-container">
                            <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/235.png" alt="Smeargle" class="pokemon-card-img">
                        </div>
                        <div class="divider"></div>
                        <h3 class="card-title mt-3">Paletas de Color</h3>
                        <p class="description-card">Descubre paletas inspiradas en los colores de los Pokémon y disfruta de combinaciones únicas.</p>
                    </div>
                </a>
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        .bento-card {
            background: linear-gradient(145deg, var(--midnight-steel), var(--midnight-abyss-light));
            border-radius: var(--card-border-radius);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
            padding: 2.5rem;
            display: flex;
            flex-direction: column;
            align-items: stretch;
            height: 100%;
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            color: var(--white);
            text-decoration: none !important;
        }

        .bento-card:hover {
            transform: translateY(-5px) scale(1.01);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.4);
        }

        .bento-card .card-title,
        .bento-card .description-card {
            text-align: center;
        }

        .bento-card .card-title {
            color: var(--golden-bloom);
            font-family: var(--font-title);
            font-weight: 700;
            margin-bottom: 0.8rem;
            font-size: 1.8rem;
            min-height: 2.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            line-height: 1.2;
        }

        .bento-card .description-card {
            color: var(--white);
            font-family: var(--font-text);
            font-size: 1rem;
            line-height: 1.6;
            flex-grow: 1;
            min-height: 90px;
            display: flex;
            align-items: flex-start;
            justify-content: center;
        }

        .card-link:hover .card-title,
        .card-link:hover .description-card {
            color: var(--golden-bloom-light);
        }

        .pokemon-image-container {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            height: 140px;
            overflow: hidden;
        }

        .pokemon-card-img {
            max-height: 100%;
            max-width: 100%;
            width: auto;
            object-fit: contain;
            transition: transform 0.3s ease-in-out;
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.5s ease-out, transform 0.5s ease-out;
        }

        .pokemon-card-img.loaded {
            opacity: 1;
            transform: translateY(0);
        }

        .bento-card:hover .pokemon-card-img {
            transform: scale(1.1);
        }

        .card-content-wrapper {
            display: flex;
            flex-direction: column;
            height: 100%;
            justify-content: space-between;
            align-items: center;
        }

        .divider {
            width: 70%;
            height: 2px;
            background-color: var(--golden-bloom);
            margin: 1.2rem auto;
            border-radius: 1px;
        }

        @media (max-width: 767.98px) {
            .bento-card {
                padding: 1.8rem;
            }
            .bento-card .card-title {
                font-size: 1.5rem;
            }
            .bento-card .description-card {
                font-size: 0.95rem;
            }
            .pokemon-image-container {
                height: 100px;
            }
            .pokemon-card-img {
                max-width: 90px;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const pokemonCardImages = document.querySelectorAll('.pokemon-card-img');
            pokemonCardImages.forEach(img => {
                img.classList.add('loaded'); 
            });
        });
    </script>
@endpush