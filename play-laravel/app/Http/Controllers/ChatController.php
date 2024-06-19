<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Chat;
use App\Models\Message;

class ChatController extends Controller
{
    public function index() {
        // Get the authenticated user's ID
        $userId = Auth::id();

        // Retrieve all chats involving the authenticated user
        $chats = Chat::where('user_one_id', $userId)
                    ->orWhere('user_two_id', $userId)
                    ->get();

        // Return the chats as a JSON response or to a view
        return response()->json(['data' => $chats]);
    }

    public function store(Request $request) {
        // Validate the incoming request data
        $request->validate([
            'user_one_id' => 'required|integer|exists:users,id',
            'user_two_id' => 'required|integer|exists:users,id|different:user_one_id',
        ]);

        // Create a new chat
        $chat = Chat::create([
            'user_one_id' => $request->input('user_one_id'),
            'user_two_id' => $request->input('user_two_id'),
        ]);

        // Return the newly created chat as a JSON response
        return response()->json(['data' => $chat]);
    }

    public function getChatMessages(Request $request, $chatId)
    {
        // Retrieve the chat by its ID
        $chat = Chat::findOrFail($chatId);

        // Get the page query parameter from the request, default to 1 if not provided
        $page = $request->query('page', 1);

        // Calculate the offset based on the page number and items per page (20 messages per page)
        $offset = ($page - 1) * 20;

        // Retrieve messages related to this chat, limit to 20 messages per page
        $messages = Message::where('chat_id', $chat->id)
                    ->orderBy('created_at', 'desc') // Example: order by descending created_at
                    ->offset($offset)
                    ->limit(20)
                    ->get();

        // Return messages as JSON response
        return response()->json(['data'=> $messages]);
    }
}
