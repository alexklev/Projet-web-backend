<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nom',
        'prenom',
        'identifiant',
        'email',
        'nationalite',
        'date_naissance',
        'telephone',
        'photo',
        'password',
        'role',
    ];

    public function formations()
    {
        return $this->hasMany(Formation::class);
    }

    public function inscriptionF()
    {
        return $this->belongsToMany(Formation::class, 'inscriptions');
    }

    public function inscriptionC()
    {
        return $this->belongsToMany(Cours::class, 'inscriptions');
    }
}
