<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomepageContent extends Model
{
    protected $guarded = [];

    // 'content' stores only items/extra section config, not metadata
    protected $casts = ['content' => 'array'];

    public function media()
    {
        return $this->hasMany(Media::class);
    }
}
