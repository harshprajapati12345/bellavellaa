<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerNotification extends Model
{
    protected $fillable = ['customer_id', 'type', 'title', 'body', 'data', 'action_url', 'read_at'];
    protected $casts = ['data' => 'array', 'read_at' => 'datetime'];

    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function scopeUnread($q) { return $q->whereNull('read_at'); }
    public function markAsRead() { $this->update(['read_at' => now()]); }
}
