<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackageContext extends Model
{
    protected $guarded = [];

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function resolveContextModel(): ?Model
    {
        return match ($this->context_type) {
            'category' => Category::find($this->context_id),
            'service_group' => ServiceGroup::find($this->context_id),
            default => null,
        };
    }
}
