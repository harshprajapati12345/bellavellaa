<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Referral extends Model
{
    protected $guarded = [];

    protected $casts = [
        'reward_given_at' => 'datetime',
        'reward_coins' => 'integer',
    ];

    public function referrer()
    {
        return $this->morphTo();
    }

    public function referred()
    {
        return $this->morphTo();
    }
}
