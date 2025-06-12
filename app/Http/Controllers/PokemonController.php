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
    public const MAX_POKEAPI_ID = 1025;

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

    public function showPokemon($identifier)
    {
        try {
            $localPokemon = $this->findPokemon($identifier);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            return redirect()->route('pokedex.index')->with('error', 'Pokémon no encontrado.');
        }

        $userNicknames = Auth::check()
            ? $localPokemon->nicknames()->where('user_id', Auth::id())->orderByDesc('created_at')->get()
            : collect();

        return view('pokemon.show', [
            'pokemon' => $this->formatPokemonData($localPokemon),
            'localPokemonId' => $localPokemon->id,
            'userNicknames' => $userNicknames,
        ]);
    }

    public function getPokemon($identifier)
    {
        try {
            $pokemon = $this->findPokemon($identifier);
            return response()->json(['success' => true, 'data' => $this->formatPokemonData($pokemon)]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            return response()->json(['success' => false, 'message' => 'Pokémon no encontrado'], 404);
        } catch (\Exception $e) {
            Log::error("Error en getPokemon: {$e->getMessage()}");
            return response()->json(['success' => false, 'message' => 'Error interno del servidor'], 500);
        }
    }

    public function buscar(Request $request)
    {
        $request->validate([
            'query' => 'nullable|string|max:50',
            'type' => 'nullable|string',
            'egg_group' => 'nullable|string',
        ]);

        $filters = $this->getFilters($request);
        $term = strtolower(trim($filters['query'] ?? ''));

        $query = $this->applyFilters(Pokemon::query(), $filters);

        if ($term !== '') {
            $query->where(function ($q) use ($term) {
                if (is_numeric($term)) {
                    $q->orWhere('pokeapi_id', (int)$term);
                }
                $q->orWhereRaw('LOWER(name) LIKE ?', ["%{$term}%"]);
            });
        }

        $results = $query->where('pokeapi_id', '<=', self::MAX_POKEAPI_ID)
            ->orderBy('pokeapi_id')
            ->limit(100)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $results->map(fn($p) => $this->formatPokemonData($p)),
            'count' => $results->count(),
        ]);
    }

    private function findPokemon($identifier): Pokemon
    {
        return is_numeric($identifier)
            ? Pokemon::where('pokeapi_id', (int)$identifier)->firstOrFail()
            : Pokemon::where('name', strtolower($identifier))->firstOrFail();
    }

    private function getFilters(Request $request): array
    {
        return [
            'type' => $request->input('type', 'all'),
            'egg_group' => $request->input('egg_group', 'all'),
            'query' => trim($request->input('query', '')),
        ];
    }

    private function applyFilters($query, array $filters)
    {
        if (!empty($filters['type']) && $filters['type'] !== 'all') {
            $query->whereJsonContains('types', $filters['type']);
        }

        if (!empty($filters['egg_group']) && $filters['egg_group'] !== 'all') {
            $query->whereJsonContains('egg_groups', $filters['egg_group']);
        }

        return $query;
    }

    private function processAbilities(array $abilities): array
    {
        return array_map(function ($ability) {
            if (is_array($ability) && isset($ability['name_es'])) {
                return $ability;
            }

            $name = is_array($ability) ? ($ability['name'] ?? null) : $ability;

            return $name
                ? $this->getAbilityDataFromPokeApi($name)
                : $this->defaultAbilityData('unknown-ability');
        }, $abilities);
    }

    private function getAbilityDataFromPokeApi(string $abilityName): array
    {
        $cacheKey = 'ability_' . strtolower($abilityName);

        return Cache::remember($cacheKey, now()->addDays(30), function () use ($abilityName) {
            try {
                $response = Http::get("https://pokeapi.co/api/v2/ability/" . strtolower($abilityName));

                if (!$response->successful()) {
                    Log::warning("Fallo al obtener habilidad: $abilityName - " . $response->status());
                    return $this->defaultAbilityData($abilityName);
                }

                $data = $response->json();

                $name_es = collect($data['names'] ?? [])
                    ->firstWhere('language.name', 'es')['name'] ?? ucwords(str_replace('-', ' ', $abilityName));

                $desc = collect($data['effect_entries'] ?? [])
                    ->firstWhere('language.name', 'es')['effect']
                    ?? collect($data['flavor_text_entries'] ?? [])
                        ->firstWhere('language.name', 'es')['flavor_text']
                    ?? 'Descripción no disponible.';

                return [
                    'name' => $abilityName,
                    'name_es' => $name_es,
                    'description' => $desc,
                    'is_hidden' => $data['is_hidden'] ?? false,
                ];
            } catch (\Exception $e) {
                Log::error("Excepción al obtener habilidad: $abilityName - " . $e->getMessage());
                return $this->defaultAbilityData($abilityName);
            }
        });
    }

    private function defaultAbilityData(string $abilityName): array
    {
        return [
            'name' => $abilityName,
            'name_es' => ucwords(str_replace('-', ' ', $abilityName)),
            'description' => 'Descripción no disponible.',
            'is_hidden' => false,
        ];
    }

    private function formatPokemonData(Pokemon $pokemon): array
    {
        return [
            'id' => $pokemon->id,
            'pokeapi_id' => $pokemon->pokeapi_id,
            'name' => $pokemon->name,
            'display_name' => ucwords(str_replace('-', ' ', $pokemon->name)),
            'description' => $pokemon->description ?? 'Descripción no disponible',
            'types' => $pokemon->types ?? [],
            'egg_groups' => $pokemon->egg_groups ?? [],
            'stats' => $pokemon->stats ?? [],
            'abilities' => $this->processAbilities($pokemon->abilities ?? []),
            'image' => $pokemon->image,
            'height' => $pokemon->height ?? null,
            'weight' => $pokemon->weight ?? null,
        ];
    }
}
