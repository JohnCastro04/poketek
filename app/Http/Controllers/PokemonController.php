<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pokemon;

class PokemonController extends Controller
{
    public const PER_PAGE = 24; // Definimos la cantidad de Pokémon por página

    public function pokedex(Request $request)
    {
        $query = Pokemon::query();

        // Aplicar filtros
        $type = $request->input('type', 'all');
        $eggGroup = $request->input('egg_group', 'all');
        $searchTerm = $request->input('query', '');

        if ($type !== 'all' && !empty($type)) {
            // Asegúrate de que el formato en la base de datos coincida
            // Si 'types' es un JSON array de strings, esto funciona
            $query->whereJsonContains('types', $type);
        }

        if ($eggGroup !== 'all' && !empty($eggGroup)) {
            // Asegúrate de que el formato en la base de datos coincida
            // Si 'egg_groups' es un JSON array de strings, esto funciona
            $query->whereJsonContains('egg_groups', $eggGroup);
        }

        if (!empty($searchTerm)) {
            $query->where(function($q) use ($searchTerm) {
                // Si es un número, busca por ID
                if (is_numeric($searchTerm)) {
                    $q->orWhere('pokeapi_id', (int) $searchTerm);
                }
                // Busca por nombre (case-insensitive)
                $q->orWhereRaw('LOWER(name) LIKE ?', ["%".strtolower($searchTerm)."%"]);
            });
        }

        // Importante: Aplica el filtro pokeapi_id <= 1025 antes de paginar si quieres excluir los de más allá.
        // Si el campo pokeapi_id es 'id' en tu modelo, cámbialo.
        $query->where('pokeapi_id', '<=', 1025);


        // ¡Aquí está la magia de la paginación de Laravel!
        // Ordena y luego pagina los resultados.
        $pokemons = $query->orderBy('pokeapi_id')->paginate(self::PER_PAGE)->withQueryString();

        return view('pokedex', [
            'pokemons' => $pokemons, // Cambiamos el nombre de la variable a 'pokemons' para mayor claridad
            'filters' => [
                'type' => $type,
                'egg_group' => $eggGroup,
                'query' => $searchTerm,
            ],
        ]);
    }


    /**
     * Recupera la descripción en español de una habilidad desde PokéAPI.
     */
    private function getAbilityDataFromPokeApi($abilityName)
    {
        try {
            $response = @file_get_contents("https://pokeapi.co/api/v2/ability/" . strtolower($abilityName));
            if ($response === false) return null;
            $data = json_decode($response, true);

            // Nombre en español
            $name_es = null;
            if (isset($data['names'])) {
                foreach ($data['names'] as $name) {
                    if ($name['language']['name'] === 'es') {
                        $name_es = $name['name'];
                        break;
                    }
                }
            }

            // Descripción en español
            $desc = null;
            if (isset($data['effect_entries'])) {
                foreach ($data['effect_entries'] as $entry) {
                    if ($entry['language']['name'] === 'es') {
                        $desc = $entry['effect'];
                        break;
                    }
                }
            }
            if (!$desc && isset($data['flavor_text_entries'])) {
                foreach ($data['flavor_text_entries'] as $entry) {
                    if ($entry['language']['name'] === 'es') {
                        $desc = $entry['flavor_text'];
                        break;
                    }
                }
            }

            return [
                'name' => $abilityName,
                'name_es' => $name_es ?? ucfirst(str_replace('-', ' ', $abilityName)),
                'description' => $desc,
            ];
        } catch (\Exception) {
            return [
                'name' => $abilityName,
                'name_es' => ucfirst(str_replace('-', ' ', $abilityName)),
                'description' => null,
            ];
        }
    }

    public function showPokemon($id)
    {
        $pokemon = $this->findPokemon($id);

        $abilities = [];
        foreach ($pokemon->abilities as $ability) {
            $name = is_array($ability) ? $ability['name'] : $ability;
            $isHidden = is_array($ability) && isset($ability['is_hidden']) ? $ability['is_hidden'] : false;
            $data = $this->getAbilityDataFromPokeApi($name);
            $data['is_hidden'] = $isHidden; // <-- Añade esto
            $abilities[] = $data;
        }

        return view('pokemon.show', [
            'pokemon' => [
                'id' => $pokemon->pokeapi_id,
                'name' => $pokemon->name,
                'display_name' => str_replace('-', ' ', ucfirst($pokemon->name)),
                'description' => $pokemon->description,
                'types' => $pokemon->types,
                'egg_groups' => $pokemon->egg_groups,
                'stats' => $pokemon->stats,
                'abilities' => $abilities,
                'image' => $pokemon->image,
            ],
        ]);
    }

    public function getPokemon($id)
    {
        $pokemon = Pokemon::where('pokeapi_id', $id)->orWhere('name', $id)->first();

        if (!$pokemon) {
            return response()->json(['success' => false, 'message' => 'Pokémon no encontrado'], 404);
        }

        return response()->json(['success' => true, 'data' => $pokemon]);
    }

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
                    $q->orWhere('pokeapi_id', $term);
                }
                $q->orWhereRaw('LOWER(name) LIKE ?', ["%$term%"]);
            });
        }
        
        $query->where('pokeapi_id', '<=', 1025); // Aplicar filtro de ID también aquí

        $results = $query->orderBy('pokeapi_id')->limit(100)->get(); // Limite si es para sugerencias

        return response()->json([
            'success' => true,
            'data' => $results,
            'count' => $results->count(),
        ]);
    }

    private function findPokemon($id)
    {
        return Pokemon::where('pokeapi_id', $id)
            ->orWhere('name', $id)
            ->firstOrFail();
    }
}