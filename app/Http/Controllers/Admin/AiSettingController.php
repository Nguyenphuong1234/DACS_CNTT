<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiConversation;
use App\Models\AiSetting;
use Illuminate\Http\Request;

class AiSettingController extends Controller
{
    public function index()
    {
        $setting = AiSetting::query()->firstOrCreate([], [
            'is_enabled' => true,
            'model' => config('services.groq.model'),
            'system_prompt' => 'Bạn là trợ lý bán hàng trực tuyến.',
            'faq' => [],
        ]);

        $conversations = AiConversation::query()
            ->with('user')
            ->latest()
            ->paginate(12);

        return view('admin.ai.index', compact('setting', 'conversations'));
    }

    public function update(Request $request, AiSetting $setting)
    {
        $validated = $request->validate([
            'is_enabled' => ['nullable', 'boolean'],
            'model' => ['required', 'string', 'max:255'],
            'system_prompt' => ['required', 'string'],
            'faq_text' => ['nullable', 'string'],
        ]);

        $setting->update([
            'is_enabled' => $request->boolean('is_enabled'),
            'model' => $validated['model'],
            'system_prompt' => $validated['system_prompt'],
            'faq' => $this->parseFaq($validated['faq_text'] ?? ''),
        ]);

        return back()->with('success', 'Đã cập nhật cấu hình AI.');
    }

    public function destroyConversation(AiConversation $conversation)
    {
        $conversation->delete();

        return back()->with('success', 'Đã xóa hội thoại.');
    }

    private function parseFaq(string $faqText): array
    {
        return collect(preg_split('/\r\n|\r|\n/', $faqText))
            ->map(fn (string $line): string => trim($line))
            ->filter()
            ->map(function (string $line): array {
                [$question, $answer] = array_pad(explode('|', $line, 2), 2, '');

                return [
                    'question' => trim($question),
                    'answer' => trim($answer),
                ];
            })
            ->filter(fn (array $item): bool => $item['question'] !== '' && $item['answer'] !== '')
            ->values()
            ->all();
    }
}
