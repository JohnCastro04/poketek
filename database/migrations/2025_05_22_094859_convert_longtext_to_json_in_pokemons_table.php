<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ConvertLongtextToJsonInPokemonsTable extends Migration
{
    public function up()
    {
        // Asegúrate de que todos los valores actuales son JSON válidos
        DB::table('pokemons')->get()->each(function ($pokemon) {
            foreach (['types', 'egg_groups', 'stats', 'abilities'] as $column) {
                $decoded = json_decode($pokemon->$column, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Exception("Valor inválido en $column para el Pokémon ID {$pokemon->id}");
                }
            }
        });

        // Cambiar los tipos a JSON
        Schema::table('pokemons', function (Blueprint $table) {
            $table->json('types')->change();
            $table->json('egg_groups')->change();
            $table->json('stats')->change();
            $table->json('abilities')->change();
        });
    }

    public function down()
    {
        // Volver a longText en caso de rollback
        Schema::table('pokemons', function (Blueprint $table) {
            $table->longText('types')->change();
            $table->longText('egg_groups')->change();
            $table->longText('stats')->change();
            $table->longText('abilities')->change();
        });
    }
}
