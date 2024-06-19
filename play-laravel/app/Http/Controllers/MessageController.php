<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use Illuminate\Validation\Rule;

class MessageController extends Controller
{
    public function store(Request $request){
        // Validate the incoming request data
        $request->validate([
            'chat_id' => 'required|integer|exists:chats,id',
            'sender_id' => [
                'required',
                'integer',
                'exists:users,id',
                // sender_id and receiver_id must be different
                Rule::notIn([$request->input('receiver_id')])
            ],
            'receiver_id' => 'required|integer|exists:users,id',
            'content' => 'required|string',
        ]);

        // Create a new message
        $message = Message::create([
            'chat_id' => $request->input('chat_id'),
            'sender_id' => $request->input('sender_id'),
            'receiver_id' => $request->input('receiver_id'),
            'content' => $request->input('content'),
        ]);

        // Return the newly created message as a JSON response
        return response()->json(['data' => $message]);
    }
}
