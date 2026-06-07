<x-layouts::app :title="'Đơn hàng '.$order->order_code">
    @include('partials.flash')

    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-semibold">Đơn hàng {{ $order->order_code }}</h1>
                <p class="mt-1 text-sm text-zinc-500">{{ $order->user?->name }} · {{ $order->created_at->format('d/m/Y H:i') }}</p>
            </div>
            <x-status-badge :status="$order->status" />
        </div>

        <div class="grid gap-6 lg:grid-cols-[1fr_360px]">
            <section class="space-y-4">
                <div class="rounded-lg border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                    <h2 class="text-lg font-semibold">Sản phẩm</h2>
                    <div class="mt-4 divide-y divide-zinc-200 dark:divide-zinc-700">
                        @foreach ($order->items as $item)
                            <div class="flex justify-between gap-4 py-3 text-sm">
                                <div>
                                    <div class="font-medium">{{ $item->product_name }}</div>
                                    <div class="text-zinc-500">SKU {{ $item->product_sku }} · {{ number_format((float) $item->unit_price, 0, ',', '.') }} đ × {{ $item->quantity }}</div>
                                </div>
                                <div class="font-medium">{{ number_format((float) $item->line_total, 0, ',', '.') }} đ</div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-lg border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                    <h2 class="text-lg font-semibold">Lịch sử trạng thái</h2>
                    <div class="mt-4 space-y-4">
                        @foreach ($order->histories->sortBy('created_at') as $history)
                            <div class="flex gap-3">
                                <div class="mt-1 size-2 rounded-full bg-zinc-900 dark:bg-white"></div>
                                <div>
                                    <div class="font-medium">{{ $statuses[$history->to_status] ?? $history->to_status }}</div>
                                    <div class="text-sm text-zinc-500">{{ $history->created_at->format('d/m/Y H:i') }}{{ $history->changer ? ' · '.$history->changer->name : '' }}</div>
                                    @if ($history->note)
                                        <div class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">{{ $history->note }}</div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <aside class="space-y-4">
                <form class="rounded-lg border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900" method="POST" action="{{ route('admin.orders.update', $order) }}">
                    @csrf
                    @method('PUT')
                    <h2 class="text-lg font-semibold">Xử lý đơn hàng</h2>
                    <label class="mt-4 block text-sm font-medium">Trạng thái</label>
                    <select class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-950" name="status">
                        @foreach ($statuses as $status => $label)
                            <option value="{{ $status }}" @selected($order->status === $status)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <label class="mt-4 block text-sm font-medium">Ghi chú xử lý</label>
                    <textarea class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-950" name="admin_note" rows="4">{{ old('admin_note', $order->admin_note) }}</textarea>
                    <button class="mt-4 w-full rounded-md bg-zinc-900 px-4 py-2 text-sm font-medium text-white hover:bg-zinc-800" type="submit">Cập nhật đơn hàng</button>
                </form>

                <div class="rounded-lg border border-zinc-200 bg-white p-5 text-sm dark:border-zinc-700 dark:bg-zinc-900">
                    <h2 class="text-lg font-semibold">Thông tin nhận hàng</h2>
                    <div class="mt-3 font-medium">{{ $order->recipient_name }} · {{ $order->recipient_phone }}</div>
                    <div class="mt-1 text-zinc-600 dark:text-zinc-400">{{ $order->shipping_address }}</div>
                    @if ($order->customer_note)
                        <div class="mt-3 rounded-md bg-zinc-50 p-3 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400">{{ $order->customer_note }}</div>
                    @endif
                </div>

                <div class="rounded-lg border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                    <div class="flex justify-between text-sm">
                        <span>Tạm tính</span>
                        <span>{{ number_format((float) $order->subtotal, 0, ',', '.') }} đ</span>
                    </div>
                    <div class="mt-3 flex justify-between text-lg font-semibold">
                        <span>Tổng tiền</span>
                        <span>{{ number_format((float) $order->total_amount, 0, ',', '.') }} đ</span>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</x-layouts::app>
