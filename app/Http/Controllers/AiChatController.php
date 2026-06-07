<?php

namespace App\Http\Controllers;

use App\Models\AiConversation;
use App\Services\GroqChatService;
use Illuminate\Http\Request;

class AiChatController extends Controller
{
    public function store(Request $request, GroqChatService $chat)
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:1200'],
        ]);

        $reply = $chat->reply($validated['message'], $request->user());

        AiConversation::query()->create([
            'user_id' => $request->user()?->id,
            'session_id' => $request->session()->getId(),
            'message' => $validated['message'],
            'response' => $reply,
            'metadata' => [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ],
        ]);

        return response()->json(['reply' => $reply]);
    }
}
