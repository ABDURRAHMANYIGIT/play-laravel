<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Models\Chat;
use App\Events\MessageSent;

class MessageController extends Controller
{
    public function store(Request $request){
        // Validate the incoming request data
        $request->validate([
            'receiver_id' => 'required|integer|exists:users,id',
            'content' => 'required|string',
        ]);

        $sender_id = Auth::id();
        $receiver_id = (int) $request->input('receiver_id');

        // Find the chat where the authenticated user and receiver are participants
        $chat = Chat::where(function ($query) use ($sender_id, $receiver_id) {
            $query->where('user_one_id', $sender_id)
                  ->where('user_two_id', $receiver_id);
        })->orWhere(function ($query) use ($sender_id, $receiver_id) {
            $query->where('user_one_id', $receiver_id)
                  ->where('user_two_id', $sender_id);
        })->first();

        // If no chat exists, return an error
        if (!$chat) {
            return response()->json(['error' => 'No chat found between these users'], 404);
        }

        // Create a new message
        $message = Message::create([
            'chat_id' => $chat->id,
            'sender_id' => $sender_id,
            'receiver_id' => $receiver_id,
            'content' => $request->input('content'),
            'read_timestamp' => null,
        ]);

        // Dispatch MessageSent event
        broadcast(new MessageSent($message))->toOthers();

        // Return the newly created message as a JSON response
        return response()->json(['data' => $message], 200);
    }
}
