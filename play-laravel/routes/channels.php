<?php

use App\Models\Chat;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat_{chatId}', function ($user, $chatId) {
    return Chat::where(function ($query) use ($user) {
        $query->where('user_one_id', $user->id)
              ->orWhere('user_two_id', $user->id);
    })->where('id', $chatId)->exists();
});

