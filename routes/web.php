<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PokemonController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NicknameController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Página principal
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Pokédex
Route::get('/pokedex', [PokemonController::class, 'pokedex'])->name('pokedex.index');

// Pokémon aleatorio (antes de la ruta dinámica)
Route::get('/pokemon/random', function () {
    return view('pokemon.random');
})->name('pokemon.random');

// Ruta para la página de colores
Route::get('/pokemon/color', function () {
    return view('pokemon.color');
})->name('pokemon.color');

// Detalle de Pokémon (ruta dinámica, debe ir después)
Route::get('/pokemon/{nameOrId}', [PokemonController::class, 'showPokemon'])
    ->name('pokemon.show')
    ->where('nameOrId', '[a-zA-Z\-]+|\d+');

/*
|--------------------------------------------------------------------------
| Rutas autenticadas para perfil y motes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    // Perfil de usuario - edición y gestión
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Mostrar perfil (se puede acceder dentro del middleware)
    Route::get('/profile/show', [ProfileController::class, 'show'])->name('profile.show');

    // Motes
    Route::post('/pokemon/{pokemon}/nickname', [NicknameController::class, 'store'])->name('nickname.store');
    Route::delete('/nickname/{nickname}', [NicknameController::class, 'destroy'])->name('nickname.destroy');
});

/*
|--------------------------------------------------------------------------
| Dashboard (Breeze)
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| API Routes (prefijo /api)
|--------------------------------------------------------------------------
*/

Route::prefix('api')->group(function () {
    // Obtener Pokémon específico
    Route::get('/pokemon/{nameOrId}', [PokemonController::class, 'getPokemon'])
        ->where('nameOrId', '[a-zA-Z\-]+|\d+');

    // Buscar Pokémon
    Route::get('/pokemon/buscar', [PokemonController::class, 'buscar'])->name('pokemon.buscar');

    // Pokémon aleatorio (singular)
    Route::get('/random-pokemon', [PokemonController::class, 'randomPokemon']);

    // Pokémon aleatorios (múltiples) - AHORA DENTRO DEL PREFIJO 'api'
    Route::get('/random-pokemons', [PokemonController::class, 'randomPokemons']);
});

/*
|--------------------------------------------------------------------------
| Rutas admin (con middleware auth y prefijo /admin)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Gestión de usuarios
    Route::get('/users/{user}/edit', [AdminDashboardController::class, 'editUser'])->name('users.edit');
    Route::patch('/users/{user}', [AdminDashboardController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [AdminDashboardController::class, 'deleteUser'])->name('users.destroy');
});

require __DIR__.'/auth.php';

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/picture', [ProfileController::class, 'updateProfilePicture'])->name('profile.picture.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});