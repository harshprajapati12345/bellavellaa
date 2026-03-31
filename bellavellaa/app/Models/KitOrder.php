<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KitOrder extends Model
{
    protected $fillable = [
        'professional_id',
        'kit_product_id',
        'idempotency_key',
        'idempotency_hash',
        'idempotency_response',
        'quantity',
        'total_amount',
        'payment_id',
        'razorpay_order_id',
        'payment_session_id',
        'payment_status',
        'payment_method',
        'order_status',
        'status',
        'assigned_at',
        'notes',
    ];

    public function professional()
    {
        return $this->belongsTo(Professional::class);
    }

    public function user()
    {
        return $this->belongsTo(Professional::class, 'professional_id');
    }

    public function product()
    {
        return $this->belongsTo(KitProduct::class, 'kit_product_id');
    }

    public function kitProduct()
    {
        return $this->belongsTo(KitProduct::class, 'kit_product_id');
    }
}
