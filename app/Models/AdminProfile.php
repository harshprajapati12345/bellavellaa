<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminProfile extends Model
{
    protected $fillable = [
        'admin_id', 'role_id', 'is_super_admin',
        'last_login_at', 'last_login_ip',
    ];

    protected $casts = [
        'is_super_admin' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    public function admin(): BelongsTo { return $this->belongsTo(Admin::class); }
    public function role(): BelongsTo { return $this->belongsTo(Role::class); }
}
