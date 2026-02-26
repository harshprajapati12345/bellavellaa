<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KitUnit extends Model
{
    protected $fillable = [
        'kit_product_id', 'serial_number', 'qr_code',
        'status', 'expiry_date',
    ];

    protected $casts = ['expiry_date' => 'date'];

    public function product(): BelongsTo { return $this->belongsTo(KitProduct::class, 'kit_product_id'); }
    public function assignments(): HasMany { return $this->hasMany(ProfessionalKitUnit::class); }

    public function isExpired(): bool { return $this->expiry_date && now()->isAfter($this->expiry_date); }
    public function isAvailable(): bool { return $this->status === 'available' && !$this->isExpired(); }

    public function scopeAvailable($q) { return $q->where('status', 'available'); }
    public function scopeExpired($q) { return $q->where('expiry_date', '<', now()); }
}
