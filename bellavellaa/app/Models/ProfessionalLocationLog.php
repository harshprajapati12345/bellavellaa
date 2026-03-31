<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfessionalLocationLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'professional_id', 'order_id',
        'latitude', 'longitude', 'logged_at',
    ];

    protected $casts = ['logged_at' => 'datetime'];

    public function professional(): BelongsTo { return $this->belongsTo(Professional::class); }
    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
}
