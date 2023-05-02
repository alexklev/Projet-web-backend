<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cours_formations extends Model
{
    use HasFactory;

    protected $fillable = [
        'cour_id',
        'formation_id',
    ];
}
