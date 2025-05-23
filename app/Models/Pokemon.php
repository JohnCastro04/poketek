<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pokemon extends Model
{
    protected $table = 'pokemons'; // <-- asegurarte que está en plural

    protected $fillable = [
        'pokeapi_id', 'name', 'description',
        'types', 'egg_groups', 'stats', 'image', 'abilities'
    ];

    protected $casts = [
        'types' => 'array',
        'egg_groups' => 'array',
        'stats' => 'array',
        'abilities' => 'array',
    ];
}
