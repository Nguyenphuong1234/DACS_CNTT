@extends('layouts.store')

@section('content')
    <h1 class="mb-6 text-2xl font-semibold">Địa chỉ nhận hàng</h1>

    <div class="grid gap-6 lg:grid-cols-[420px_1fr]">
        <form class="rounded-lg border border-zinc-200 bg-white p-5" method="POST" action="{{ route('addresses.store') }}">
            @csrf
            <h2 class="text-lg font-semibold">Thêm địa chỉ</h2>
            <div class="mt-4 grid gap-3">
                <input class="rounded-md border border-zinc-300 px-3 py-2 text-sm" name="recipient_name" placeholder="Tên người nhận" required>
                <input class="rounded-md border border-zinc-300 px-3 py-2 text-sm" name="recipient_phone" placeholder="Số điện thoại" required>
                <input class="rounded-md border border-zinc-300 px-3 py-2 text-sm" name="address_line" placeholder="Số nhà, tên đường" required>
                <input class="rounded-md border border-zinc-300 px-3 py-2 text-sm" name="ward" placeholder="Phường/xã">
                <input class="rounded-md border border-zinc-300 px-3 py-2 text-sm" name="district" placeholder="Quận/huyện">
                <input class="rounded-md border border-zinc-300 px-3 py-2 text-sm" name="city" placeholder="Tỉnh/thành phố" required>
                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" name="is_default" value="1">
                    Đặt làm mặc định
                </label>
            </div>
            <button class="mt-4 rounded-md bg-zinc-900 px-4 py-2 text-sm font-medium text-white hover:bg-zinc-800" type="submit">Lưu địa chỉ</button>
        </form>

        <section class="space-y-4">
            @forelse ($addresses as $address)
                <div class="rounded-lg border border-zinc-200 bg-white p-5">
                    <form class="grid gap-3 sm:grid-cols-2" method="POST" action="{{ route('addresses.update', $address) }}">
                        @csrf
                        @method('PUT')
                        <input class="rounded-md border border-zinc-300 px-3 py-2 text-sm" name="recipient_name" value="{{ $address->recipient_name }}" required>
                        <input class="rounded-md border border-zinc-300 px-3 py-2 text-sm" name="recipient_phone" value="{{ $address->recipient_phone }}" required>
                        <input class="rounded-md border border-zinc-300 px-3 py-2 text-sm sm:col-span-2" name="address_line" value="{{ $address->address_line }}" required>
                        <input class="rounded-md border border-zinc-300 px-3 py-2 text-sm" name="ward" value="{{ $address->ward }}">
                        <input class="rounded-md border border-zinc-300 px-3 py-2 text-sm" name="district" value="{{ $address->district }}">
                        <input class="rounded-md border border-zinc-300 px-3 py-2 text-sm sm:col-span-2" name="city" value="{{ $address->city }}" required>
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" name="is_default" value="1" @checked($address->is_default)>
                            Địa chỉ mặc định
                        </label>
                        <div class="flex justify-end gap-2 sm:col-span-2">
                            <button class="rounded-md border border-zinc-300 px-4 py-2 text-sm font-medium hover:bg-zinc-50" type="submit">Cập nhật</button>
                        </div>
                    </form>
                    <form class="mt-2 text-right" method="POST" action="{{ route('addresses.destroy', $address) }}">
                        @csrf
                        @method('DELETE')
                        <button class="text-sm font-medium text-rose-700 hover:underline" type="submit">Xóa địa chỉ</button>
                    </form>
                </div>
            @empty
                <div class="rounded-lg border border-dashed border-zinc-300 bg-white p-8 text-center text-zinc-500">Chưa có địa chỉ nhận hàng.</div>
            @endforelse
        </section>
    </div>
@endsection
