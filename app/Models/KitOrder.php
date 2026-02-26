<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KitOrder extends Model
{
    protected $fillable = [
        'professional_id', 'kit_product_id', 'quantity', 'used_quantity', 'status', 'assigned_at', 'notes'
    ];

    public function professional()
    {
        return $this->belongsTo(Professional::class);
    }

    public function kitProduct()
    {
        return $this->belongsTo(KitProduct::class);
    }
}
