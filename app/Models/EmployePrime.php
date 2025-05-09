<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployePrime extends Model
{
    use HasFactory;

    protected $table = 'prime_employe'; // nom de la table pivot

    protected $fillable = [
        'employe_id',
        'prime_id',
        'montant',
        'date_attribution',
        'remarques',
    ];

    public function prime()
    {
        return $this->belongsTo(Prime::class);
    }

    public function employe()
    {
        return $this->belongsTo(Employe::class);
    }
}

