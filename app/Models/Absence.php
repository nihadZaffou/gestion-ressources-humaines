<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Absence extends Model
{
    use HasFactory;

    protected $fillable = [
        'employe_id',
        'date_debut',
        'date_fin',
        'motif',
        'justificatif',
        'cree_par_admin',
        'justifiee',
        'impact_salaire',
    ];

    protected $casts = [
        'cree_par_admin' => 'boolean',
        'justifiee' => 'boolean',
        'impact_salaire' => 'boolean',
        'date_debut' => 'date',
        'date_fin' => 'date',
    ];

    /**
     * L'absence appartient à un employé.
     * On relie l'absence à l'utilisateur via `employe_id`.
     */
    public function employe()
    {
        return $this->belongsTo(Employe::class, 'employe_id');

    }

    /**
     * Accesseur : récupérer l'URL publique du justificatif
     */
    public function getJustificatifUrlAttribute()
    {
        return $this->justificatif 
            ? asset('storage/' . $this->justificatif) 
            : null;
    }
}

