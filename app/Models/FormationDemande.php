<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormationDemande extends Model
{
    use HasFactory;
    protected $fillable = [
        'employe_id',
        'formation_id',
        'statut',
        'motif',
    ];

    // Une demande appartient à un employé
    public function employe()
    {
        return $this->belongsTo(Employe::class);
    }

    // Une demande appartient à une formation
    public function formation()
    {
        return $this->belongsTo(Formation::class);
    }
}
