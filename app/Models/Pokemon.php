<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pokemon extends Model
{
    use HasFactory;
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

    public function nicknames()
    {
        return $this->hasMany(Nickname::class);
    }
    public function userNicknames()
    {
        return $this->hasMany(Nickname::class)->where('user_id', auth()->id());
    }
}
