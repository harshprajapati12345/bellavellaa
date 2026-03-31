<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfessionalDocument extends Model
{
    protected $fillable = [
        'professional_id', 'type', 'file_path',
        'status', 'rejection_reason', 'verified_at', 'verified_by',
    ];

    protected $casts = ['verified_at' => 'datetime'];

    public function professional(): BelongsTo { return $this->belongsTo(Professional::class); }
    public function verifier(): BelongsTo { return $this->belongsTo(Admin::class, 'verified_by'); }

    public function scopePending($q) { return $q->where('status', 'pending'); }
    public function scopeApproved($q) { return $q->where('status', 'approved'); }
}
