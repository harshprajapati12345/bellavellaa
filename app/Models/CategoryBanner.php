<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryBanner extends Model
{
    protected $fillable = [
        'category_id',
        'title',
        'subtitle',
        'image',
        'link_url',
        'sort_order',
        'status',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
