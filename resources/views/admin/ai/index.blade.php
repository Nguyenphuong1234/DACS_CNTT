<x-layouts::app title="Quản lý Chatbox AI">
    @include('partials.flash')

    <div class="grid gap-6 lg:grid-cols-[420px_1fr]">
        <form class="rounded-lg border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900" method="POST" action="{{ route('admin.ai.update', $setting) }}">
            @csrf
            @method('PUT')

            <h1 class="text-2xl font-semibold">Chatbox AI</h1>
            <p class="mt-1 text-sm text-zinc-500">Cấu hình prompt, model và câu hỏi thường gặp.</p>

            <label class="mt-5 flex items-center gap-2 text-sm">
                <input type="checkbox" name="is_enabled" value="1" @checked($setting->is_enabled)>
                Bật Chatbox AI
            </label>

            <div class="mt-4">
                <label class="mb-1 block text-sm font-medium">Model Groq</label>
                <input class="w-full rounded-md border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-950" name="model" value="{{ old('model', $setting->model) }}" required>
            </div>

            <div class="mt-4">
                <label class="mb-1 block text-sm font-medium">System prompt</label>
                <textarea class="w-full rounded-md border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-950" name="system_prompt" rows="7" required>{{ old('system_prompt', $setting->system_prompt) }}</textarea>
            </div>

            <div class="mt-4">
                <label class="mb-1 block text-sm font-medium">FAQ</label>
                <textarea class="w-full rounded-md border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-950" name="faq_text" rows="8">{{ old('faq_text', collect($setting->faq ?? [])->map(fn ($item) => ($item['question'] ?? '').' | '.($item['answer'] ?? ''))->implode("\n")) }}</textarea>
                <p class="mt-1 text-xs text-zinc-500">Mỗi dòng: Câu hỏi | Câu trả lời</p>
            </div>

            <button class="mt-5 rounded-md bg-zinc-900 px-4 py-2 text-sm font-medium text-white hover:bg-zinc-800" type="submit">Lưu cấu hình AI</button>
        </form>

        <section class="rounded-lg border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-lg font-semibold">Lịch sử hội thoại</h2>
                    <p class="mt-1 text-sm text-zinc-500">Các câu hỏi gần đây từ khách hàng.</p>
                </div>
                <span class="rounded-md bg-zinc-100 px-2 py-1 text-xs text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">{{ $conversations->total() }} hội thoại</span>
            </div>

            <div class="mt-4 divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse ($conversations as $conversation)
                    <article class="py-4">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <div class="text-sm font-medium">{{ $conversation->user?->name ?? 'Khách chưa đăng nhập' }}</div>
                                <div class="text-xs text-zinc-500">{{ $conversation->created_at->format('d/m/Y H:i') }}</div>
                            </div>
                            <form method="POST" action="{{ route('admin.ai.conversations.destroy', $conversation) }}">
                                @csrf
                                @method('DELETE')
                                <button class="text-xs font-medium text-rose-700 hover:underline" type="submit">Xóa</button>
                            </form>
                        </div>
                        <div class="mt-3 rounded-md bg-zinc-50 p-3 text-sm dark:bg-zinc-800">
                            <div class="font-medium">Khách hỏi</div>
                            <div class="mt-1 text-zinc-600 dark:text-zinc-300">{{ $conversation->message }}</div>
                        </div>
                        <div class="mt-2 rounded-md bg-zinc-50 p-3 text-sm dark:bg-zinc-800">
                            <div class="font-medium">AI trả lời</div>
                            <div class="mt-1 whitespace-pre-line text-zinc-600 dark:text-zinc-300">{{ $conversation->response }}</div>
                        </div>
                    </article>
                @empty
                    <div class="py-8 text-center text-sm text-zinc-500">Chưa có hội thoại AI.</div>
                @endforelse
            </div>

            <div class="mt-4">{{ $conversations->links() }}</div>
        </section>
    </div>
</x-layouts::app>
