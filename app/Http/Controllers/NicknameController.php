<?php

namespace App\Http\Controllers;

use App\Models\Nickname;
use App\Models\Pokemon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class NicknameController extends Controller
{
    public function store(Request $request, Pokemon $pokemon)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:20',
                Rule::unique('nicknames')->where(function ($query) use ($pokemon) {
                    return $query->where('user_id', Auth::id())
                               ->where('pokemon_id', $pokemon->id);
                })
            ],
        ]);

        try {
            $nickname = Nickname::create([
                'user_id' => Auth::id(),
                'pokemon_id' => $pokemon->id,
                'name' => $request->name,
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Mote añadido con éxito.',
                    'nickname' => $nickname
                ]);
            }

            return redirect()->route('pokemon.show', $pokemon->pokeapi_id)
                           ->with('status', 'Mote añadido con éxito.');

        } catch (\Exception $e) {
            Log::error("Error adding nickname for Pokemon ID {$pokemon->id} by user " . Auth::id() . ": " . $e->getMessage());

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo añadir el mote.'
                ], 500);
            }

            return redirect()->route('pokemon.show', $pokemon->pokeapi_id)
                           ->with('error', 'No se pudo añadir el mote.');
        }
    }

    public function destroy(Nickname $nickname)
    {
        $pokemonIdentifier = $nickname->pokemon->pokeapi_id;

        if ($nickname->user_id !== Auth::id()) {
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permiso para borrar este mote.'
                ], 403);
            }

            return redirect()->route('pokemon.show', $pokemonIdentifier)
                             ->with('error', 'No tienes permiso para borrar este mote.');
        }

        try {
            $nickname->delete();

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Mote borrado con éxito.'
                ]);
            }

            return redirect()->route('pokemon.show', $pokemonIdentifier)
                           ->with('status', 'Mote borrado con éxito.');

        } catch (\Exception $e) {
            Log::error("Error deleting nickname ID {$nickname->id} by user " . Auth::id() . ": " . $e->getMessage());

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo borrar el mote.'
                ], 500);
            }

            return redirect()->route('pokemon.show', $pokemonIdentifier)
                           ->with('error', 'No se pudo borrar el mote.');
        }
    }
}