<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Formation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nom',
        'niveau',
        'statut',
        'date_debut',
        'date_fin',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function inscription()
    {
        return $this->belongsToMany(User::class, 'inscriptions');
    }

    public function cours()
    {
        return $this->belongsToMany(Cours::class, 'cours_formations');
    }
}
