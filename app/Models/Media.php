<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $guarded = [];

    public function homepageContent()
    {
        return $this->belongsTo(HomepageContent::class);
    }
}
