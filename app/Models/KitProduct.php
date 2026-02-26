<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KitProduct extends Model
{
    protected $fillable = [
        'sku', 'name', 'brand', 'category', 'unit', 'price', 
        'total_stock', 'min_stock', 'expiry_date', 'status', 'last_restocked'
    ];

    public function kitOrders()
    {
        return $this->hasMany(KitOrder::class);
    }

    public function getAssignedStockAttribute()
    {
        return $this->kitOrders()->where('status', 'Assigned')->sum('quantity');
    }

    public function getAvailableStockAttribute()
    {
        return $this->total_stock - $this->assigned_stock;
    }
}
