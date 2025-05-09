<?php

namespace App\Models;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;
    protected $fillable = [
        'employe_id',
        'nom',
        'motif',
        'quantite',
        'statut',
        'explication', 
    ];
    

public function employee()
{
    return $this->belongsTo(User::class, 'employe_id');
}
}
