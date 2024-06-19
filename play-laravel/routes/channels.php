<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat_{chat_id}', function ($user, $chat_id) {
    return (int) $user->id === (int) $chat_id;
});
