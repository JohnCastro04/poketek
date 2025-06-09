<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pokemon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log; // Importado para el registro de errores
use Illuminate\Support\Facades\Cache; // Importado para la caché

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
            $query->where(function($q) use ($searchTerm) {
                if (is_numeric($searchTerm)) {
                    $q->orWhere('pokeapi_id', (int) $searchTerm); // Convertir a entero para búsqueda por ID
                }
                $q->orWhereRaw('LOWER(name) LIKE ?', ["%".strtolower($searchTerm)."%"]);
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
     * Recupera datos de una habilidad desde PokéAPI, utilizando caché.
     */
    private function getAbilityDataFromPokeApi(string $abilityName): array
    {
        // Define una clave de caché única para esta habilidad
        $cacheKey = 'pokeapi_ability_' . strtolower($abilityName);

        // Intenta recuperar los datos de la caché; si no existen, ejecuta la función y almacena el resultado
        return Cache::remember($cacheKey, now()->addHours(24), function () use ($abilityName) {
            try {
                // Realiza la llamada a la API con un timeout de 5 segundos
                $response = Http::timeout(5)->get("https://pokeapi.co/api/v2/ability/" . strtolower($abilityName));

                // Si la respuesta no fue exitosa (código de estado 4xx o 5xx)
                if (!$response->successful()) {
                    Log::warning("PokeAPI request failed for ability {$abilityName}: " . $response->status());
                    return [
                        'name' => $abilityName,
                        'name_es' => ucfirst(str_replace('-', ' ', $abilityName)),
                        'description' => null, // No se pudo obtener la descripción
                    ];
                }
                $data = $response->json();

                $name_es = null;
                // Busca el nombre en español
                if (isset($data['names'])) {
                    foreach ($data['names'] as $nameEntry) {
                        if ($nameEntry['language']['name'] === 'es') {
                            $name_es = $nameEntry['name'];
                            break;
                        }
                    }
                }

                $desc = null;
                // Prioriza la descripción de 'effect_entries' en español
                if (isset($data['effect_entries'])) {
                    foreach ($data['effect_entries'] as $entry) {
                        if ($entry['language']['name'] === 'es' && !empty($entry['effect'])) {
                            $desc = $entry['effect'];
                            break;
                        }
                    }
                }
                // Si no se encontró descripción en 'effect_entries', busca en 'flavor_text_entries'
                if (!$desc && isset($data['flavor_text_entries'])) {
                    foreach ($data['flavor_text_entries'] as $entry) {
                        if ($entry['language']['name'] === 'es' && !empty($entry['flavor_text'])) {
                            $desc = $entry['flavor_text'];
                            break;
                        }
                    }
                }

                return [
                    'name' => $abilityName, // Nombre original/clave de la habilidad
                    'name_es' => $name_es ?? ucfirst(str_replace('-', ' ', $abilityName)), // Nombre en español o formateado
                    'description' => $desc,
                ];
            } catch (\Illuminate\Http\Client\RequestException $e) {
                // Captura errores específicos del cliente HTTP (ej. fallos de conexión)
                Log::error("PokeAPI connection error for ability {$abilityName}: " . $e->getMessage());
                return [
                    'name' => $abilityName,
                    'name_es' => ucfirst(str_replace('-', ' ', $abilityName)),
                    'description' => 'Error al conectar con la API para obtener la descripción.',
                ];
            } catch (\Exception $e) {
                // Captura cualquier otro error inesperado
                Log::error("Generic error fetching ability data for {$abilityName}: " . $e->getMessage());
                return [
                    'name' => $abilityName,
                    'name_es' => ucfirst(str_replace('-', ' ', $abilityName)),
                    'description' => 'No se pudo obtener la descripción de la habilidad.',
                ];
            }
        });
    }

    /**
     * Muestra la página de detalles de un Pokémon.
     * El $identifier puede ser el pokeapi_id o el nombre del Pokémon.
     */
    public function showPokemon($identifier)
    {
        // Encuentra el Pokémon en la base de datos local
        $localPokemon = $this->findPokemon($identifier);

        // Procesar habilidades para obtener datos de la API (descripciones en español, etc.)
        $processedAbilities = [];
        if (is_array($localPokemon->abilities)) { // Asegurarse de que 'abilities' es un array JSON válido
            foreach ($localPokemon->abilities as $abilityInfo) {
                $abilityName = '';
                $isHidden = false;

                // Adapta la lectura según la estructura real de tus datos de habilidad en la BD
                if (is_array($abilityInfo)) {
                    $abilityName = $abilityInfo['name'] ?? ($abilityInfo['ability']['name'] ?? null);
                    $isHidden = $abilityInfo['is_hidden'] ?? false;
                } elseif (is_string($abilityInfo)) {
                    $abilityName = $abilityInfo;
                }

                if ($abilityName) {
                    $abilityDataFromApi = $this->getAbilityDataFromPokeApi($abilityName);
                    $abilityDataFromApi['is_hidden'] = $isHidden; // Añade el estado 'oculta'
                    $processedAbilities[] = $abilityDataFromApi;
                }
            }
        }

        // Preparar los datos del Pokémon para la vista
        $pokemonViewData = [
            'id' => $localPokemon->pokeapi_id, // ID de PokeAPI
            'name' => $localPokemon->name, // Nombre en inglés (clave)
            'display_name' => str_replace('-', ' ', ucfirst($localPokemon->name)), // Nombre formateado para mostrar
            'description' => $localPokemon->description,
            'types' => $localPokemon->types,
            'egg_groups' => $localPokemon->egg_groups,
            'stats' => $localPokemon->stats,
            'abilities' => $processedAbilities, // Habilidades procesadas con descripciones
            'image' => $localPokemon->image,
        ];

        // Obtener motes del usuario para este Pokémon si está autenticado
        $userNicknames = collect(); // Inicializa una colección vacía
        if (Auth::check()) {
            $userNicknames = $localPokemon->nicknames() // Usa la relación 'nicknames' del modelo Pokemon
                                          ->where('user_id', Auth::id()) // Filtra por el usuario actual
                                          ->orderBy('created_at', 'desc')
                                          ->get();
        }

        return view('pokemon.show', [
            'pokemon' => $pokemonViewData, // Datos del Pokémon
            'localPokemonId' => $localPokemon->id, // ID del Pokémon en TU base de datos
            'userNicknames' => $userNicknames, // Motes del usuario para este Pokémon
        ]);
    }

    /**
     * Endpoint API para obtener un Pokémon por ID o nombre.
     */
    public function getPokemon($id)
    {
        // Busca el Pokémon por pokeapi_id o por nombre
        $pokemon = Pokemon::where('pokeapi_id', $id)->orWhere('name', $id)->first();

        if (!$pokemon) {
            return response()->json(['success' => false, 'message' => 'Pokémon no encontrado'], 404);
        }

        return response()->json(['success' => true, 'data' => $pokemon]);
    }

    /**
     * Endpoint API para buscar Pokémon con filtros (usado para AJAX, etc.).
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

        // Aplicar filtro por tipo
        if ($type !== 'all' && !empty($type)) {
            $query->whereJsonContains('types', $type);
        }

        // Aplicar filtro por grupo de huevo
        if ($eggGroup !== 'all' && !empty($eggGroup)) {
            $query->whereJsonContains('egg_groups', $eggGroup);
        }

        // Aplicar búsqueda por ID o nombre
        if ($term !== '') {
            $query->where(function ($q) use ($term) {
                if (is_numeric($term)) {
                    $q->orWhere('pokeapi_id', (int) $term); // Convertir a entero
                }
                $q->orWhereRaw('LOWER(name) LIKE ?', ["%$term%"]);
            });
        }
        
        // Limitar a los Pokémon de la primera generación
        $query->where('pokeapi_id', '<=', 1025);
        
        // Obtener resultados (limitado a 100 para API)
        $results = $query->orderBy('pokeapi_id')->limit(100)->get();

        return response()->json([
            'success' => true,
            'data' => $results,
            'count' => $results->count(),
        ]);
    }

    /**
     * Encuentra un Pokémon en la base de datos local por pokeapi_id o nombre.
     * Aborta con 404 si no se encuentra.
     */
    private function findPokemon($identifier): Pokemon
    {
        // Determinar si el identificador es numérico (pokeapi_id) o string (name)
        if (is_numeric($identifier)) {
            return Pokemon::where('pokeapi_id', (int) $identifier)->firstOrFail();
        }
        return Pokemon::where('name', strtolower($identifier))->firstOrFail();
    }
}