<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KitType extends Model
{
    protected $fillable = ['kit_category_id', 'name', 'description'];
    public function category(): BelongsTo { return $this->belongsTo(KitCategory::class, 'kit_category_id'); }
}
