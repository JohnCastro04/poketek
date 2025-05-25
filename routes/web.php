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

// Página de Pokémon aleatorio (debe ir ANTES de la ruta dinámica)
Route::get('/pokemon/random', function () {
    return view('pokemon.random');
})->name('pokemon.random');

// Detalle de Pokémon (debe ir después)
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

    // API: Pokémon aleatorio con filtros y cantidad
    Route::get('/random-pokemon', function (\Illuminate\Http\Request $request) {
        $type = $request->input('type', 'all');
        $eggGroup = $request->input('egg_group', 'all');
        $qty = max(1, min((int)$request->input('qty', 1), 6)); // Solo permite de 1 a 6

        $query = \App\Models\Pokemon::query()->where('pokeapi_id', '<=', 1025);

        if ($type !== 'all' && !empty($type)) {
            $query->whereJsonContains('types', $type);
        }
        if ($eggGroup !== 'all' && !empty($eggGroup)) {
            $query->whereJsonContains('egg_groups', $eggGroup);
        }

        $count = $query->count();
        if ($count === 0) {
            return response()->json(['success' => false, 'message' => 'No hay Pokémon con esos filtros']);
        }

        // Obtener IDs aleatorios únicos
        $offsets = [];
        if ($qty >= $count) {
            $offsets = range(0, $count - 1);
            shuffle($offsets);
            $offsets = array_slice($offsets, 0, $qty);
        } else {
            while (count($offsets) < $qty) {
                $rand = rand(0, $count - 1);
                if (!in_array($rand, $offsets)) $offsets[] = $rand;
            }
        }

        $pokemons = [];
        foreach ($offsets as $offset) {
            $pokemon = (clone $query)->skip($offset)->first();
            if ($pokemon) {
                $pokemons[] = [
                    'id' => $pokemon->pokeapi_id,
                    'display_name' => ucwords(str_replace('-', ' ', $pokemon->name)),
                    'image' => $pokemon->image,
                    'types' => $pokemon->types,
                    'description' => $pokemon->description,
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data' => $pokemons,
        ]);
    });
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