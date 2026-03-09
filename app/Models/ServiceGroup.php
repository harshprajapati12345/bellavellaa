<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ServiceGroup extends Model
{
    protected $guarded = [];

    // ─── Relationships ───────────────────────────────────────────────

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Active services belonging to this group.
     */
    public function services()
    {
        return $this->hasMany(Service::class, 'service_group_id');
    }

    // ─── Auto-slug ───────────────────────────────────────────────────

    /**
     * Generate a globally unique slug from category slug + group name.
     * Called by ServiceGroupController before create/update.
     */
    public static function generateSlug(Category $category, string $name): string
    {
        $base = Str::slug($category->slug . '-' . $name);
        $slug = $base;
        $i = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }
}
