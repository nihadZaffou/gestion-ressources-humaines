<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'employe_id',
        'salaire_base',
        'primes',
        'remboursements',
        'absences',
        'salaire_net',
        'mois',
        'annee',
    ];

    public function employe()
    {
        return $this->belongsTo(Employe::class);
    }
}
