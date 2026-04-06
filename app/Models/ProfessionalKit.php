<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
        
class ProfessionalKit extends Model
{
    protected $fillable = [
        'professional_id',
        'product_id',
        'qty',
    ];

    public function professional()
    {
        return $this->belongsTo(Professional::class);
    }

    public function product()
    {
        return $this->belongsTo(KitProduct::class, 'product_id');
    }
}
