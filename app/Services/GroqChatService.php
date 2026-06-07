<?php

namespace App\Services;

use App\Models\AiSetting;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class GroqChatService
{
    public function reply(string $message, ?User $user = null): string
    {
        $setting = AiSetting::query()->first();

        if (! $setting?->is_enabled || ! filter_var(env('AI_CHAT_ENABLED', true), FILTER_VALIDATE_BOOLEAN)) {
            return 'Chatbox AI hiện đang tắt. Vui lòng liên hệ quản trị viên để được hỗ trợ.';
        }

        $apiKey = config('services.groq.key');

        if (! $apiKey) {
            return 'Hệ thống chưa cấu hình GROQ_API_KEY nên chưa thể trả lời bằng AI.';
        }

        $response = Http::withToken($apiKey)
            ->acceptJson()
            ->timeout(config('services.groq.timeout', 20))
            ->post(rtrim(config('services.groq.base_url'), '/').'/chat/completions', [
                'model' => $setting->model ?: config('services.groq.model'),
                'messages' => [
                    ['role' => 'system', 'content' => $this->systemPrompt($setting, $user)],
                    ['role' => 'user', 'content' => Str::limit($message, 1200, '')],
                ],
                'temperature' => 0.4,
                'max_tokens' => 700,
            ]);

        if (! $response->successful()) {
            return 'AI đang tạm thời không phản hồi. Bạn có thể thử lại sau hoặc liên hệ cửa hàng.';
        }

        return trim((string) data_get($response->json(), 'choices.0.message.content'))
            ?: 'AI chưa có câu trả lời phù hợp cho câu hỏi này.';
    }

    private function systemPrompt(AiSetting $setting, ?User $user): string
    {
        $products = Product::query()
            ->with('category')
            ->active()
            ->latest()
            ->limit(12)
            ->get()
            ->map(fn (Product $product): string => sprintf(
                '- %s | SKU %s | %s | %s VND | tồn kho %s | %s',
                $product->name,
                $product->sku,
                $product->category?->name ?? 'Chưa phân loại',
                number_format((float) $product->price, 0, ',', '.'),
                $product->stock_quantity,
                Str::limit((string) $product->description, 120),
            ))
            ->implode("\n");

        $faq = collect($setting->faq ?? [])
            ->map(fn (array $item): string => '- Hỏi: '.($item['question'] ?? '').' | Đáp: '.($item['answer'] ?? ''))
            ->filter()
            ->implode("\n");

        $orders = $user
            ? $user->orders()
                ->latest()
                ->limit(5)
                ->get()
                ->map(fn (Order $order): string => sprintf(
                    '- %s | %s | %s VND | ngày %s',
                    $order->order_code,
                    $order->statusLabel(),
                    number_format((float) $order->total_amount, 0, ',', '.'),
                    $order->created_at->format('d/m/Y'),
                ))
                ->implode("\n")
            : 'Khách chưa đăng nhập nên không được tra cứu đơn hàng.';

        return implode("\n\n", [
            $setting->system_prompt,
            'Quy tắc: trả lời bằng tiếng Việt, ngắn gọn, không bịa sản phẩm hoặc đơn hàng. Khi hỏi về đơn hàng, chỉ dùng danh sách đơn hàng được cung cấp.',
            "Sản phẩm đang có:\n".($products ?: 'Chưa có dữ liệu sản phẩm.'),
            "FAQ:\n".($faq ?: 'Chưa có FAQ.'),
            "Đơn hàng của người dùng hiện tại:\n".($orders ?: 'Người dùng chưa có đơn hàng.'),
        ]);
    }
}
