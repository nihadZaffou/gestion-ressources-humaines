<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prime extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'description'];

    public function employePrimes()
    {
        return $this->hasMany(EmployePrime::class);
    }

    public function employes()
    {
        return $this->belongsToMany(Employe::class, 'prime_employe')
                    ->withPivot('date_attribution');
    }
}

