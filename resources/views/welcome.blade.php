@extends('layouts.app')

@section('title', 'Poketek - Inicio')

@section('content')
    <section class="header-section text-center">
        <div class="container">
            <h1 class="display-4">¡Bienvenidos a Pokétek!</h1>
            <p class="description-card">Tu pokedex personal para descubrir, aprender y jugar.</p>
        </div>
    </section>

    <section id="features" class="container py-5">
        <div class="row justify-content-center g-4">
            <div class="col-md-6 col-lg-4">
                <div class="bento-card text-center">
                    <div class="pokemon-img-container mb-3">
                        <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/25.png" alt="Pikachu" class="pokemon-img loaded">
                    </div>
                    <h3 class="card-title">Descubre Pokémon</h3>
                    <p class="description-card">Navega por una extensa base de datos con detalles de cada especie. ¡Aprende sus tipos, habilidades y estadísticas!</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="bento-card text-center">
                    <div class="pokemon-img-container mb-3">
                        <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/6.png" alt="Charizard" class="pokemon-img loaded">
                    </div>
                    <h3 class="card-title">Explora Habilidades</h3>
                    <p class="description-card">Conoce las diversas habilidades de los Pokémon y cómo influyen en sus batallas.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="bento-card text-center">
                    <div class="pokemon-img-container mb-3">
                        <img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/143.png" alt="Snorlax" class="pokemon-img loaded">
                    </div>
                    <h3 class="card-title">Gestiona tu Equipo</h3>
                    <p class="description-card">Pronto podrás organizar tus equipos y estrategias para ser el mejor entrenador.</p>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    {{-- Optional: Add any specific JavaScript for this page here --}}
    <script>
        document.querySelectorAll('.scroll-to-section').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Simple image lazy loading/animation on load for a better feel
        document.addEventListener('DOMContentLoaded', () => {
            const pokemonImages = document.querySelectorAll('.pokemon-img');
            pokemonImages.forEach(img => {
                if (img.complete) {
                    img.classList.add('loaded');
                } else {
                    img.addEventListener('load', () => {
                        img.classList.add('loaded');
                    });
                }
            });
        });
    </script>
@endpush