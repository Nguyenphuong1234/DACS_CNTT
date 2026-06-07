@extends('layouts.store')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-semibold">Giỏ hàng</h1>
        <a class="text-sm font-medium text-zinc-600 hover:text-zinc-950" href="{{ route('home') }}">Tiếp tục mua sắm</a>
    </div>

    @if ($cart->items->isEmpty())
        <div class="rounded-lg border border-dashed border-zinc-300 bg-white p-8 text-center text-zinc-600">Giỏ hàng đang trống.</div>
    @else
        <div class="grid gap-6 lg:grid-cols-[1fr_360px]">
            <div class="space-y-4">
                @foreach ($cart->items as $item)
                    <div class="flex gap-4 rounded-lg border border-zinc-200 bg-white p-4">
                        <img class="size-24 rounded-md object-cover" src="{{ $item->product->imageUrl() }}" alt="{{ $item->product->name }}">
                        <div class="min-w-0 flex-1">
                            <div class="font-semibold">{{ $item->product->name }}</div>
                            <div class="mt-1 text-sm text-zinc-500">Tồn kho: {{ $item->product->stock_quantity }}</div>
                            <div class="mt-2 font-medium">{{ number_format((float) $item->product->price, 0, ',', '.') }} đ</div>
                        </div>
                        <div class="w-36 space-y-2">
                            <form method="POST" action="{{ route('cart.update', $item->product) }}">
                                @csrf
                                @method('PUT')
                                <input class="w-full rounded-md border border-zinc-300 px-3 py-2 text-sm" type="number" name="quantity" min="1" max="{{ max(1, $item->product->stock_quantity) }}" value="{{ $item->quantity }}">
                                <button class="mt-2 w-full rounded-md border border-zinc-300 px-3 py-2 text-sm font-medium hover:bg-zinc-50">Cập nhật</button>
                            </form>
                            <form method="POST" action="{{ route('cart.destroy', $item->product) }}">
                                @csrf
                                @method('DELETE')
                                <button class="w-full rounded-md border border-rose-200 px-3 py-2 text-sm font-medium text-rose-700 hover:bg-rose-50">Xóa</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <aside class="rounded-lg border border-zinc-200 bg-white p-5">
                <h2 class="text-lg font-semibold">Tạm tính</h2>
                <div class="mt-4 flex justify-between text-sm">
                    <span>Số sản phẩm</span>
                    <span>{{ $cart->items->sum('quantity') }}</span>
                </div>
                <div class="mt-3 flex justify-between text-lg font-semibold">
                    <span>Tổng tiền</span>
                    <span>{{ number_format($cart->subtotal(), 0, ',', '.') }} đ</span>
                </div>
                <a class="mt-5 block rounded-md bg-zinc-900 px-4 py-2 text-center text-sm font-medium text-white hover:bg-zinc-800" href="{{ route('checkout.index') }}">Tiến hành đặt hàng</a>
            </aside>
        </div>
    @endif
@endsection
