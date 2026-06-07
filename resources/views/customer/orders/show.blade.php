@extends('layouts.store')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-semibold">Đơn hàng {{ $order->order_code }}</h1>
            <div class="mt-1 text-sm text-zinc-500">Đặt lúc {{ $order->created_at->format('d/m/Y H:i') }}</div>
        </div>
        <x-status-badge :status="$order->status" />
    </div>

    <div class="grid gap-6 lg:grid-cols-[1fr_360px]">
        <section class="space-y-4">
            <div class="rounded-lg border border-zinc-200 bg-white p-5">
                <h2 class="text-lg font-semibold">Sản phẩm</h2>
                <div class="mt-4 divide-y divide-zinc-200">
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

            <div class="rounded-lg border border-zinc-200 bg-white p-5">
                <h2 class="text-lg font-semibold">Lịch sử trạng thái</h2>
                <div class="mt-4 space-y-4">
                    @foreach ($order->histories->sortBy('created_at') as $history)
                        <div class="flex gap-3">
                            <div class="mt-1 size-2 rounded-full bg-zinc-900"></div>
                            <div>
                                <div class="font-medium">{{ \App\Models\Order::statuses()[$history->to_status] ?? $history->to_status }}</div>
                                <div class="text-sm text-zinc-500">{{ $history->created_at->format('d/m/Y H:i') }}{{ $history->changer ? ' · '.$history->changer->name : '' }}</div>
                                @if ($history->note)
                                    <div class="mt-1 text-sm text-zinc-600">{{ $history->note }}</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <aside class="space-y-4">
            <div class="rounded-lg border border-zinc-200 bg-white p-5">
                <h2 class="text-lg font-semibold">Giao hàng</h2>
                <div class="mt-3 text-sm text-zinc-700">
                    <div class="font-medium">{{ $order->recipient_name }} · {{ $order->recipient_phone }}</div>
                    <div class="mt-1">{{ $order->shipping_address }}</div>
                </div>
            </div>
            <div class="rounded-lg border border-zinc-200 bg-white p-5">
                <h2 class="text-lg font-semibold">Thanh toán</h2>
                <div class="mt-3 flex justify-between text-sm">
                    <span>Phương thức</span>
                    <span>COD</span>
                </div>
                <div class="mt-3 flex justify-between text-lg font-semibold">
                    <span>Tổng tiền</span>
                    <span>{{ number_format((float) $order->total_amount, 0, ',', '.') }} đ</span>
                </div>
            </div>
            @if (in_array($order->status, [\App\Models\Order::PENDING, \App\Models\Order::CONFIRMED], true))
                <form method="POST" action="{{ route('orders.cancel', $order) }}">
                    @csrf
                    <button class="w-full rounded-md border border-rose-200 px-4 py-2 text-sm font-medium text-rose-700 hover:bg-rose-50" type="submit">Hủy đơn hàng</button>
                </form>
            @endif
        </aside>
    </div>
@endsection
