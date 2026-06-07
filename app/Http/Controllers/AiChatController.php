<?php

namespace App\Http\Controllers;

use App\Models\AiConversation;
use App\Services\GroqChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class AiChatController extends Controller
{
    public function store(Request $request, GroqChatService $chat): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:1200'],
        ]);

        try {
            $reply = $chat->reply($validated['message'], $request->user());
        } catch (Throwable $exception) {
            Log::error('AI chat reply failed.', [
                'message' => $exception->getMessage(),
            ]);

            return response()->json([
                'reply' => 'AI đang tạm thời không phản hồi. Bạn có thể thử lại sau hoặc liên hệ cửa hàng.',
            ]);
        }

        try {
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
        } catch (Throwable $exception) {
            Log::error('AI conversation save failed.', [
                'message' => $exception->getMessage(),
            ]);
        }

        return response()->json(['reply' => $reply]);
    }
}
