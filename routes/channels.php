<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Private channel for professional-specific events (e.g., job assignments)
Broadcast::channel('professionals.{id}', function ($user, $id) {
    // Only professionals can listen to their own channel
    return (int) $user->id === (int) $id && $user instanceof \App\Models\Professional;
}, ['guards' => ['professional-api']]);
