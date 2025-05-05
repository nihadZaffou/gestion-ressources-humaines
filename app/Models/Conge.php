<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conge extends Model
{
    use HasFactory;
    protected $fillable = [
        'employe_id', 'date_debut', 'date_fin', 'motif', 'statut','explication'
    ];

    public function employe()
    {
        return $this->belongsTo(Employe::class);
    }
}
