<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Pokemon;

class ImportarPokemons extends Command
{
    protected $signature = 'importar:pokemons';
    protected $description = 'Importa los primeros 151 Pokémon desde la PokéAPI y los guarda en la base de datos';

    public function handle()
    {
        $this->info('Importando Pokémon...');

        for ($id = 1; $id <= 151; $id++) {
            $this->info("Importando Pokémon #$id...");

            // Primer fetch: /pokemon
            $pokemonResponse = Http::get("https://pokeapi.co/api/v2/pokemon/{$id}");
            if (!$pokemonResponse->ok()) {
                $this->error("Error al obtener el Pokémon #$id");
                continue;
            }

            $pokemonData = $pokemonResponse->json();

            // Segundo fetch: /pokemon-species
            $speciesResponse = Http::get("https://pokeapi.co/api/v2/pokemon-species/{$id}");
            if (!$speciesResponse->ok()) {
                $this->error("Error al obtener la especie del Pokémon #$id");
                continue;
            }

            $speciesData = $speciesResponse->json();

            // Descripción en español
            $description = collect($speciesData['flavor_text_entries'])
                ->first(fn($entry) => $entry['language']['name'] === 'es')['flavor_text'] ?? null;

            // Limpiar descripción
            $description = $description ? str_replace(["\n", "\f"], ' ', $description) : null;

            // Tipos
            $types = array_map(fn($t) => $t['type']['name'], $pokemonData['types']);

            // Grupos huevo
            $eggGroups = array_map(fn($g) => $g['name'], $speciesData['egg_groups'] ?? []);

            // Stats base
            $stats = [];
            foreach ($pokemonData['stats'] as $stat) {
                $stats[$stat['stat']['name']] = $stat['base_stat'];
            }

            // Habilidades
            $abilities = array_map(fn($a) => [
                'name' => $a['ability']['name'],
                'is_hidden' => $a['is_hidden']
            ], $pokemonData['abilities']);

            // Imagen oficial
            $image = $pokemonData['sprites']['other']['official-artwork']['front_default']
                ?? $pokemonData['sprites']['front_default'];

            // Guardar en la base de datos
            Pokemon::updateOrCreate(
                ['pokeapi_id' => $pokemonData['id']],
                [
                    'name' => $pokemonData['name'],
                    'description' => $description,
                    'types' => $types,
                    'egg_groups' => $eggGroups,
                    'stats' => $stats,
                    'image' => $image,
                    'abilities' => $abilities,
                ]
            );
        }

        $this->info('Importación completada.');
        return 0;
    }
}

