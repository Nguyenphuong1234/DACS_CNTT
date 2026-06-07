<?php

namespace App\Services;

use App\Models\AiSetting;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

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

        try {
            $response = $this->httpClient($apiKey)
                ->post(rtrim(config('services.groq.base_url'), '/').'/chat/completions', [
                    'model' => $setting->model ?: config('services.groq.model'),
                    'messages' => [
                        ['role' => 'system', 'content' => $this->systemPrompt($setting, $user)],
                        ['role' => 'user', 'content' => Str::limit($message, 1200, '')],
                    ],
                    'temperature' => 0.4,
                    'max_tokens' => 700,
                ]);
        } catch (ConnectionException $exception) {
            Log::warning('Groq API connection failed.', [
                'message' => $exception->getMessage(),
            ]);

            return 'AI đang tạm thời không phản hồi. Bạn có thể thử lại sau hoặc liên hệ cửa hàng.';
        } catch (Throwable $exception) {
            Log::error('Groq API request failed.', [
                'message' => $exception->getMessage(),
            ]);

            return 'AI đang tạm thời không phản hồi. Bạn có thể thử lại sau hoặc liên hệ cửa hàng.';
        }

        if (! $response->successful()) {
            Log::warning('Groq API returned an error response.', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return 'AI đang tạm thời không phản hồi. Bạn có thể thử lại sau hoặc liên hệ cửa hàng.';
        }

        return trim((string) data_get($response->json(), 'choices.0.message.content'))
            ?: 'AI chưa có câu trả lời phù hợp cho câu hỏi này.';
    }

    private function httpClient(string $apiKey): PendingRequest
    {
        return Http::withToken($apiKey)
            ->acceptJson()
            ->timeout(config('services.groq.timeout', 20))
            ->withOptions([
                'verify' => $this->resolveSslVerify(),
            ]);
    }

    private function resolveSslVerify(): bool|string
    {
        if (! config('services.groq.verify_ssl', true)) {
            return false;
        }

        $caBundle = config('services.groq.ca_bundle');

        if (is_string($caBundle) && $caBundle !== '' && is_file($caBundle)) {
            return $caBundle;
        }

        $defaultBundle = storage_path('certs/cacert.pem');

        if (is_file($defaultBundle)) {
            return $defaultBundle;
        }

        return true;
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
