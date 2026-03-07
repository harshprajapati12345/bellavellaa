<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RewardRule extends Model
{
    protected $fillable = [
        'type',
        'title',
        'coins',
        'status',
        'max_per_user',
    ];
}
