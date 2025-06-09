<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pokemon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class PokemonController extends Controller
{
    public const PER_PAGE = 24;

    /**
     * Muestra la página principal del Pokédex con filtros y paginación.
     */
    public function pokedex(Request $request)
    {
        $query = Pokemon::query();

        $type = $request->input('type', 'all');
        $eggGroup = $request->input('egg_group', 'all');
        $searchTerm = $request->input('query', '');

        // Aplicar filtro por tipo si no es 'all' y no está vacío
        if ($type !== 'all' && !empty($type)) {
            $query->whereJsonContains('types', $type);
        }

        // Aplicar filtro por grupo de huevo si no es 'all' y no está vacío
        if ($eggGroup !== 'all' && !empty($eggGroup)) {
            $query->whereJsonContains('egg_groups', $eggGroup);
        }

        // Aplicar búsqueda por ID o nombre
        if (!empty($searchTerm)) {
            $query->where(function ($q) use ($searchTerm) {
                if (is_numeric($searchTerm)) {
                    $q->orWhere('pokeapi_id', (int) $searchTerm);
                }
                $q->orWhereRaw('LOWER(name) LIKE ?', ["%" . strtolower($searchTerm) . "%"]);
            });
        }

        // Limitar a los Pokémon de la primera generación (ID hasta 1025)
        $query->where('pokeapi_id', '<=', 1025);

        // Obtener Pokémon paginados y añadir los parámetros de consulta a los enlaces de paginación
        $pokemons = $query->orderBy('pokeapi_id')->paginate(self::PER_PAGE)->withQueryString();

        return view('pokedex', [
            'pokemons' => $pokemons,
            'filters' => [
                'type' => $type,
                'egg_group' => $eggGroup,
                'query' => $searchTerm,
            ],
        ]);
    }

    /**
     * Retorna un Pokémon aleatorio con todos sus datos.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function randomPokemon(Request $request)
    {
        $type = $request->input('type', 'all');
        $eggGroup = $request->input('egg_group', 'all');

        $query = Pokemon::query();

        // Aplicar filtros si existen
        if ($type !== 'all' && !empty($type)) {
            $query->whereJsonContains('types', $type);
        }

        if ($eggGroup !== 'all' && !empty($eggGroup)) {
            $query->whereJsonContains('egg_groups', $eggGroup);
        }

        // Limitar a la primera generación
        $query->where('pokeapi_id', '<=', 1025);

        // Obtener un Pokémon aleatorio
        $pokemon = $query->inRandomOrder()->first();

        if (!$pokemon) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontraron Pokémon con los filtros aplicados'
            ], 404);
        }

        // Formatear los datos para la respuesta
        $pokemonData = [
            'pokeapi_id' => $pokemon->pokeapi_id,
            'name' => $pokemon->name,
            'display_name' => ucwords(str_replace('-', ' ', $pokemon->name)),
            'description' => $pokemon->description ?? 'Descripción no disponible',
            'types' => $pokemon->types ?? [],
            'egg_groups' => $pokemon->egg_groups ?? [],
            'stats' => $pokemon->stats ?? [],
            'abilities' => $pokemon->abilities ?? [],
            'image' => $pokemon->image,
            'height' => $pokemon->height ?? null,
            'weight' => $pokemon->weight ?? null,
        ];

        return response()->json([
            'success' => true,
            'data' => $pokemonData
        ]);
    }

    /**
     * Retorna múltiples Pokémon aleatorios con todos sus datos, basados en filtros.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function randomPokemons(Request $request) // Nuevo método
    {
        $type = $request->input('type', 'all');
        $eggGroup = $request->input('egg_group', 'all');
        $qty = (int) $request->input('qty', 1); // Cantidad de Pokémon a retornar

        // Asegurarse de que la cantidad sea al menos 1
        $qty = max(1, $qty);

        $query = Pokemon::query();

        // Aplicar filtros si existen
        if ($type !== 'all' && !empty($type)) {
            $query->whereJsonContains('types', $type);
        }

        if ($eggGroup !== 'all' && !empty($eggGroup)) {
            $query->whereJsonContains('egg_groups', $eggGroup);
        }

        // Limitar a la primera generación
        $query->where('pokeapi_id', '<=', 1025);

        // Obtener Pokémon aleatorios
        $pokemons = $query->inRandomOrder()->take($qty)->get();

        if ($pokemons->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontraron Pokémon con los filtros aplicados'
            ], 404);
        }

        // Formatear los datos para la respuesta
        $formattedPokemons = $pokemons->map(function ($pokemon) {
            return [
                'pokeapi_id' => $pokemon->pokeapi_id,
                'name' => $pokemon->name,
                'display_name' => ucwords(str_replace('-', ' ', $pokemon->name)),
                'description' => $pokemon->description ?? 'Descripción no disponible',
                'types' => $pokemon->types ?? [],
                'egg_groups' => $pokemon->egg_groups ?? [],
                'stats' => $pokemon->stats ?? [],
                'abilities' => $pokemon->abilities ?? [],
                'image' => $pokemon->image,
                'height' => $pokemon->height ?? null,
                'weight' => $pokemon->weight ?? null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formattedPokemons
        ]);
    }

    /**
     * Encuentra un Pokémon en la base de datos local por pokeapi_id o nombre.
     */
    private function findPokemon($identifier): Pokemon
    {
        if (is_numeric($identifier)) {
            return Pokemon::where('pokeapi_id', (int) $identifier)->firstOrFail();
        }
        return Pokemon::where('name', strtolower($identifier))->firstOrFail();
    }

    /**
     * Muestra la página de detalles de un Pokémon.
     */
    public function showPokemon($identifier)
    {
        $localPokemon = $this->findPokemon($identifier);

        $pokemonViewData = [
            'id' => $localPokemon->pokeapi_id,
            'name' => $localPokemon->name,
            'display_name' => str_replace('-', ' ', ucfirst($localPokemon->name)),
            'description' => $localPokemon->description,
            'types' => $localPokemon->types,
            'egg_groups' => $localPokemon->egg_groups,
            'stats' => $localPokemon->stats,
            'abilities' => $localPokemon->abilities,
            'image' => $localPokemon->image,
        ];

        $userNicknames = collect();
        if (Auth::check()) {
            $userNicknames = $localPokemon->nicknames()
                ->where('user_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('pokemon.show', [
            'pokemon' => $pokemonViewData,
            'localPokemonId' => $localPokemon->id,
            'userNicknames' => $userNicknames,
        ]);
    }

    /**
     * Endpoint API para obtener un Pokémon por ID o nombre.
     */
    public function getPokemon($id)
    {
        $pokemon = Pokemon::where('pokeapi_id', $id)->orWhere('name', $id)->first();

        if (!$pokemon) {
            return response()->json(['success' => false, 'message' => 'Pokémon no encontrado'], 404);
        }

        return response()->json(['success' => true, 'data' => $pokemon]);
    }

    /**
     * Endpoint API para buscar Pokémon con filtros.
     */
    public function buscar(Request $request)
    {
        $request->validate([
            'query' => 'nullable|string|max:50',
            'type' => 'nullable|string',
            'egg_group' => 'nullable|string',
        ]);

        $term = strtolower(trim($request->input('query', '')));
        $type = $request->input('type', 'all');
        $eggGroup = $request->input('egg_group', 'all');

        $query = Pokemon::query();

        if ($type !== 'all' && !empty($type)) {
            $query->whereJsonContains('types', $type);
        }

        if ($eggGroup !== 'all' && !empty($eggGroup)) {
            $query->whereJsonContains('egg_groups', $eggGroup);
        }

        if ($term !== '') {
            $query->where(function ($q) use ($term) {
                if (is_numeric($term)) {
                    $q->orWhere('pokeapi_id', (int) $term);
                }
                $q->orWhereRaw('LOWER(name) LIKE ?', ["%$term%"]);
            });
        }

        $query->where('pokeapi_id', '<=', 1025);
        $results = $query->orderBy('pokeapi_id')->limit(100)->get();

        return response()->json([
            'success' => true,
            'data' => $results,
            'count' => $results->count(),
        ]);
    }

}