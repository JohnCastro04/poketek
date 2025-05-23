<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PokemonController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Pokédex
Route::get('/pokedex', [PokemonController::class, 'pokedex'])->name('pokedex.index');

// Detalle de Pokémon
Route::get('/pokemon/{nameOrId}', [PokemonController::class, 'showPokemon'])
     ->name('pokemon.show')
     ->where('nameOrId', '[a-zA-Z\-]+|\d+');

// Perfil de usuario
Route::get('/profile', [ProfileController::class, 'show'])
    ->middleware('auth')
    ->name('profile.show');

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('api')->group(function () {
    // Obtener Pokémon específico
    Route::get('/pokemon/{nameOrId}', [PokemonController::class, 'getPokemon'])
         ->where('nameOrId', '[a-zA-Z\-]+|\d+');

    // Buscar Pokémon
    Route::get('/pokemon/buscar', [PokemonController::class, 'buscar'])->name('pokemon.buscar');
});

// Rutas de autenticación
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/profile/show', [ProfileController::class, 'show'])->name('profile.show');
});

require __DIR__.'/auth.php';