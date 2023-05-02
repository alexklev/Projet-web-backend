<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cours extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nom',
        'actif',
    ];

    public function formations()
    {
        return $this->belongsToMany(Formation::class, 'cours_formations');
    }

    public function inscription()
    {
        return $this->belongsToMany(User::class, 'inscriptions');
    }
}
