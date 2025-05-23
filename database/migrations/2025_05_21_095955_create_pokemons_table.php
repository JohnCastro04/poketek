<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePokemonsTable extends Migration
{
    public function up(): void
    {
        Schema::create('pokemons', function (Blueprint $table) {
            $table->id();
            $table->integer('pokeapi_id')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('types');
            $table->json('egg_groups')->nullable();
            $table->json('stats');
            $table->string('image')->nullable();
            $table->json('abilities');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pokemons');
    }
}
