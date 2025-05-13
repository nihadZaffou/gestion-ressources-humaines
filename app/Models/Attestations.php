<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attestations extends Model
{
    use HasFactory;
    protected $fillable = [
        'type_id',
        'employe_id',
        'date_demande',
        'date_livraison',
        'statut',
        'pdf',
    ];

    public function employe()
    {
        return $this->belongsTo(Employe::class);
    }
    public function attestationType()
    {
        return $this->belongsTo(AttestationType::class, 'type_id');
    }
}
