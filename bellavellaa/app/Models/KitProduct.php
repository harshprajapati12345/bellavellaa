<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KitProduct extends Model
{
    protected $fillable = [
        'sku', 'name', 'description', 'image', 'brand', 'category_id', 'unit', 'price', 
        'total_stock', 'min_stock', 'expiry_date', 'status', 'last_restocked'
    ];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute()   
    {
        if (!$this->image) {
            return 'https://plus.unsplash.com/premium_photo-1661340702301-53a479f3781f?q=80&w=1486&auto=format&fit=crop';
        }
        
        if (str_starts_with($this->image, 'http')) {
            return $this->image;
        }

        return asset('storage/' . $this->image);
    }

    public function category()
    {
        return $this->belongsTo(KitCategory::class);
    }

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
