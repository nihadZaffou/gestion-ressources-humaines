<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Remboursement extends Model
{
    use HasFactory;
    protected $fillable=([
        'employe_id', 
        'type', 
        'montant', 
        'justification', 
        'status',
    ]);
    public function employe(){
        return $this->belongsto(Employe::class);
    }
    /**
     * Vérifie si le remboursement a été approuvé.
     *
     * @return bool
     */
    public function isApproved()
    {
        return $this->status === 'approuvé';
    }

    /**
     * Vérifie si le remboursement a été refusé.
     *
     * @return bool
     */
    public function isRejected()
    {
        return $this->status === 'refusé';
    }

    /**
     * Vérifie si le remboursement est en attente.
     *
     * @return bool
     */
    public function isPending()
    {
        return $this->status === 'en attente';
    }
}

