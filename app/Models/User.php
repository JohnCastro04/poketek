<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'permission',
        'profile_picture',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'permission' => 'boolean', 
        ];
    }

    /**
     * Get the profile picture URL
     */
    public function getProfilePictureUrlAttribute(): string
    {
        return asset("images/profile/{$this->profile_picture}.png");
    }

    /**
     * Get available profile pictures (1-20 por ejemplo)
     */
    public static function getAvailableProfilePictures(): array
    {
        return range(1, 20); // Ajusta según cuántas imágenes tengas
    }

    /**
     * Assign random profile picture
     */
    public function assignRandomProfilePicture(): void
    {
        $this->profile_picture = rand(1, 20); // Ajusta según cuántas imágenes tengas
        $this->save();
    }
}