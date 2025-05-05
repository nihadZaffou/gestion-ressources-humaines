<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Employe extends Authenticatable implements JWTSubject
{
    use HasFactory ;use HasApiTokens;

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

    // Obligatoire pour JWTSubject :
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
