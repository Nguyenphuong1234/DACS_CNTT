<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-zinc-50 text-zinc-950 antialiased">
        <header class="sticky top-0 z-40 border-b border-zinc-200 bg-white/95 backdrop-blur">
            <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">
                <a href="{{ route('home') }}" class="flex items-center gap-3 font-semibold">
                    <span class="flex size-9 items-center justify-center rounded-lg bg-zinc-900 text-sm font-bold text-white">OS</span>
                    <span>Online Store</span>
                </a>

                <nav class="flex items-center gap-2 text-sm font-medium text-zinc-700">
                    <a class="rounded-md px-3 py-2 hover:bg-zinc-100" href="{{ route('home') }}">Sản phẩm</a>
                    @auth
                        <a class="rounded-md px-3 py-2 hover:bg-zinc-100" href="{{ route('orders.index') }}">Đơn hàng</a>
                        <a class="rounded-md px-3 py-2 hover:bg-zinc-100" href="{{ route('addresses.index') }}">Địa chỉ</a>
                        <a class="rounded-md px-3 py-2 hover:bg-zinc-100" href="{{ route('cart.index') }}">Giỏ hàng</a>
                        @if (auth()->user()->isAdmin())
                            <a class="rounded-md px-3 py-2 hover:bg-zinc-100" href="{{ route('admin.dashboard') }}">Quản trị</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="rounded-md px-3 py-2 hover:bg-zinc-100" type="submit">Đăng xuất</button>
                        </form>
                    @else
                        <a class="rounded-md px-3 py-2 hover:bg-zinc-100" href="{{ route('login') }}">Đăng nhập</a>
                        <a class="rounded-md bg-zinc-900 px-3 py-2 text-white hover:bg-zinc-800" href="{{ route('register') }}">Đăng ký</a>
                    @endauth
                </nav>
            </div>
        </header>

        <main class="mx-auto min-h-[calc(100vh-160px)] max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            @include('partials.flash')
            {{ $slot ?? '' }}
            @yield('content')
        </main>

        <footer class="border-t border-zinc-200 bg-white">
            <div class="mx-auto flex max-w-7xl flex-col gap-2 px-4 py-6 text-sm text-zinc-600 sm:px-6 lg:px-8">
                <div class="font-semibold text-zinc-900">Online Store</div>
                <div>Thanh toán khi nhận hàng, quản lý đơn hàng và tư vấn sản phẩm bằng AI.</div>
            </div>
        </footer>

        @php
            $aiSessionId = session()->getId();
            $aiConversations = \App\Models\AiConversation::query()
                ->where('session_id', $aiSessionId)
                ->when(auth()->check(), function ($query) {
                    $query->orWhere(function ($query) {
                        $query->where('user_id', auth()->id())
                            ->whereNull('session_id');
                    });
                })
                ->latest()
                ->limit(20)
                ->get()
                ->reverse();
        @endphp

        <section class="fixed bottom-4 right-4 z-50 w-[min(380px,calc(100vw-2rem))] rounded-lg border border-zinc-200 bg-white shadow-xl">
            <div class="flex items-center justify-between border-b border-zinc-200 px-4 py-3">
                <div>
                    <div class="text-sm font-semibold">Chatbox AI</div>
                    <div class="text-xs text-zinc-500">Tư vấn sản phẩm và đơn hàng</div>
                </div>
                <button id="ai-chat-toggle" class="rounded-md border border-zinc-200 px-2 py-1 text-xs font-medium hover:bg-zinc-50" type="button">Ẩn</button>
            </div>
            <div id="ai-chat-body" class="space-y-3 p-4">
                <div id="ai-chat-messages" class="max-h-64 space-y-3 overflow-y-auto text-sm">
                    @if ($aiConversations->isEmpty())
                        <div class="rounded-lg bg-zinc-100 px-3 py-2 text-zinc-700">Xin chào, mình có thể hỗ trợ bạn chọn sản phẩm hoặc tra cứu đơn hàng.</div>
                    @else
                        @foreach ($aiConversations as $conversation)
                            <div class="ml-8 rounded-lg bg-zinc-900 px-3 py-2 text-white">{!! nl2br(e($conversation->message)) !!}</div>
                            <div class="rounded-lg bg-zinc-100 px-3 py-2 text-zinc-700">{!! nl2br(e($conversation->response ?? 'AI chưa phản hồi.')) !!}</div>
                        @endforeach
                    @endif
                </div>
                <form id="ai-chat-form" class="flex gap-2">
                    <input id="ai-chat-message" class="min-w-0 flex-1 rounded-md border border-zinc-300 px-3 py-2 text-sm outline-none focus:border-zinc-900" name="message" maxlength="1200" placeholder="Nhập câu hỏi..." required>
                    <button class="rounded-md bg-zinc-900 px-3 py-2 text-sm font-medium text-white hover:bg-zinc-800" type="submit">Gửi</button>
                </form>
            </div>
        </section>

        <script>
            const toggle = document.getElementById('ai-chat-toggle');
            const body = document.getElementById('ai-chat-body');
            const form = document.getElementById('ai-chat-form');
            const input = document.getElementById('ai-chat-message');
            const messages = document.getElementById('ai-chat-messages');
            const escapeHtml = (value) => value
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');

            if (messages) {
                messages.scrollTop = messages.scrollHeight;
            }

            toggle?.addEventListener('click', () => {
                const hidden = body.classList.toggle('hidden');
                toggle.textContent = hidden ? 'Mở' : 'Ẩn';
            });

            form?.addEventListener('submit', async (event) => {
                event.preventDefault();
                const message = input.value.trim();

                if (! message) return;

                messages.insertAdjacentHTML('beforeend', `<div class="ml-8 rounded-lg bg-zinc-900 px-3 py-2 text-white">${escapeHtml(message).replace(/\n/g, '<br>')}</div>`);
                input.value = '';

                try {
                    const response = await fetch('{{ route('ai.chat') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({ message }),
                    });

                    const data = await response.json();
                    messages.insertAdjacentHTML('beforeend', `<div class="rounded-lg bg-zinc-100 px-3 py-2 text-zinc-700">${escapeHtml(data.reply || 'AI chưa phản hồi.').replace(/\n/g, '<br>')}</div>`);
                } catch (error) {
                    messages.insertAdjacentHTML('beforeend', '<div class="rounded-lg bg-rose-50 px-3 py-2 text-rose-700">Không thể kết nối AI lúc này.</div>');
                }

                messages.scrollTop = messages.scrollHeight;
            });
        </script>

        @fluxScripts
    </body>
</html>
