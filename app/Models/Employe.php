<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Employe extends Authenticatable implements JWTSubject
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'telephone',
        'poste',
        'salaire',
        'date_entree',
        'password',
    ];

    protected $hidden = [
        'password',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function absences()
    {
        return $this->hasMany(Absence::class, 'employe_id');
    }

    public function demandes()
    {
        return $this->hasMany(FormationDemande::class);
    }

    // Modification de la relation primes
    public function primes()
    {
        return $this->belongsToMany(Prime::class, 'prime_employe', 'employe_id', 'prime_id')
                    ->withPivot('date_attribution', 'montant', 'remarque');
    }
}

