<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KitCategory extends Model
{
    protected $fillable = ['name', 'slug', 'description'];
    public function types(): HasMany { return $this->hasMany(KitType::class); }
}
