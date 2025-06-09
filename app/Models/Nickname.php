<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Nickname extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'pokemon_id', // Este es el ID de tu tabla 'pokemons' (PK)
        'name',
    ];

    /**
     * Get the user that owns the nickname.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the pokemon that this nickname refers to.
     */
    public function pokemon(): BelongsTo
    {
        return $this->belongsTo(Pokemon::class);
    }
}