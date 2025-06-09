<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('nicknames', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('pokemon_id')->constrained('pokemons')->onDelete('cascade'); // Se refiere a la tabla 'pokemons'
            $table->string('name');
            $table->timestamps();

            $table->unique(['user_id', 'pokemon_id', 'name']); // Un usuario no puede poner el mismo mote varias veces al mismo Pokémon
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nicknames');
    }
};