<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VerificationRequest extends Model
{
    protected $fillable = [
        'professional_id',
        'type',
        'status',
        'rejection_reason',
    ];

    public function professional()
    {
        return $this->belongsTo(Professional::class);
    }
}
