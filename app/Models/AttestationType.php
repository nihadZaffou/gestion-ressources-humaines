<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttestationType extends Model // <-- هنا تأكد الاسم بحال هكا
{
    use HasFactory;

    protected $table = 'attestationtypes'; // <-- هاد الاسم خاصو يطابق اسم الجدول فـ migration

    protected $fillable = [
        'type',
        'description',
    ];

    public function attestations()
    {
        return $this->hasMany(Attestations::class, 'type_id');
    }
}