<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recrutement extends Model
{
    use HasFactory;
    protected $fillable = [
        'titre',
        'poste',
        'descriptionPoste',
        'descriptionProfil',
        'date_debut',
        'date_fin',
        'statut'
    ];
}
