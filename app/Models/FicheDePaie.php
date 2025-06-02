<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FicheDePaie extends Model
{
    use HasFactory;
    protected $fillable = [
        'employe_id',
        'mois',
        'annee',
        'salaire_base',
        'primes',
        'remboursements',
        'absences',
        'penalite',
        'salaire_net',
        'remarque',
    ];

    public function employe()
    {
        return $this->belongsTo(Employe::class);
    }
}
