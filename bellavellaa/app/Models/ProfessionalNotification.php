<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfessionalNotification extends Model
{
    protected $fillable = ['professional_id', 'type', 'title', 'body', 'data', 'action_url', 'read_at'];
    protected $casts = ['data' => 'array', 'read_at' => 'datetime'];

    public function professional(): BelongsTo { return $this->belongsTo(Professional::class); }
    public function scopeUnread($q) { return $q->whereNull('read_at'); }
    public function markAsRead() { $this->update(['read_at' => now()]); }
}
