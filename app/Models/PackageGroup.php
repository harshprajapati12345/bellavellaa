<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PackageGroup extends Model
{
    protected $guarded = [];

    public function linkedNode(): ?Model
    {
        return match ($this->linked_type) {
            'service_group' => ServiceGroup::find($this->linked_id),
            'service_type' => ServiceType::find($this->linked_id),
            default => null,
        };
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PackageItem::class)->orderBy('sort_order');
    }

    public function linkedItems(): HasMany
    {
        return $this->items()->where('source_type', 'linked');
    }

    public function customItems(): HasMany
    {
        return $this->items()->where('source_type', 'custom');
    }
}
