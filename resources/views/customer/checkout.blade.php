@extends('layouts.store')

@section('content')
    <h1 class="mb-6 text-2xl font-semibold">Xác nhận đặt hàng</h1>

    <div class="grid gap-6 lg:grid-cols-[1fr_380px]">
        <form class="rounded-lg border border-zinc-200 bg-white p-5" method="POST" action="{{ route('checkout.store') }}">
            @csrf
            <h2 class="text-lg font-semibold">Thông tin giao hàng</h2>

            @if ($addresses->isNotEmpty())
                <div class="mt-4 space-y-3">
                    @foreach ($addresses as $address)
                        <label class="flex gap-3 rounded-md border border-zinc-200 p-3">
                            <input type="radio" name="address_id" value="{{ $address->id }}" @checked($address->is_default)>
                            <span>
                                <span class="block font-medium">{{ $address->recipient_name }} · {{ $address->recipient_phone }}</span>
                                <span class="block text-sm text-zinc-600">{{ $address->fullAddress() }}</span>
                            </span>
                        </label>
                    @endforeach
                </div>
            @endif

            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                <input class="rounded-md border border-zinc-300 px-3 py-2 text-sm" name="recipient_name" placeholder="Tên người nhận">
                <input class="rounded-md border border-zinc-300 px-3 py-2 text-sm" name="recipient_phone" placeholder="Số điện thoại">
                <input class="rounded-md border border-zinc-300 px-3 py-2 text-sm sm:col-span-2" name="address_line" placeholder="Số nhà, tên đường">
                <input class="rounded-md border border-zinc-300 px-3 py-2 text-sm" name="ward" placeholder="Phường/xã">
                <input class="rounded-md border border-zinc-300 px-3 py-2 text-sm" name="district" placeholder="Quận/huyện">
                <input class="rounded-md border border-zinc-300 px-3 py-2 text-sm sm:col-span-2" name="city" placeholder="Tỉnh/thành phố">
                <textarea class="rounded-md border border-zinc-300 px-3 py-2 text-sm sm:col-span-2" name="customer_note" rows="3" placeholder="Ghi chú giao hàng"></textarea>
            </div>

            <button class="mt-6 rounded-md bg-zinc-900 px-4 py-2 text-sm font-medium text-white hover:bg-zinc-800" type="submit">Xác nhận đặt hàng COD</button>
        </form>

        <aside class="rounded-lg border border-zinc-200 bg-white p-5">
            <h2 class="text-lg font-semibold">Sản phẩm trong đơn</h2>
            <div class="mt-4 space-y-3">
                @foreach ($cart->items as $item)
                    <div class="flex justify-between gap-4 text-sm">
                        <span>{{ $item->product->name }} × {{ $item->quantity }}</span>
                        <span class="font-medium">{{ number_format($item->lineTotal(), 0, ',', '.') }} đ</span>
                    </div>
                @endforeach
            </div>
            <div class="mt-5 flex justify-between border-t border-zinc-200 pt-4 text-lg font-semibold">
                <span>Tổng tiền</span>
                <span>{{ number_format($cart->subtotal(), 0, ',', '.') }} đ</span>
            </div>
            <div class="mt-2 text-sm text-zinc-500">Phương thức thanh toán: COD</div>
        </aside>
    </div>
@endsection
