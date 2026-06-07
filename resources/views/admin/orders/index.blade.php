<x-layouts::app title="Quản lý đơn hàng">
    @include('partials.flash')

    <div class="space-y-5">
        <div>
            <h1 class="text-2xl font-semibold">Đơn hàng</h1>
            <p class="mt-1 text-sm text-zinc-500">Tìm kiếm, lọc và xử lý trạng thái đơn hàng.</p>
        </div>

        <form class="grid gap-3 rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900 md:grid-cols-3" method="GET">
            <input class="rounded-md border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-950" name="search" value="{{ request('search') }}" placeholder="Mã đơn, khách hàng">
            <select class="rounded-md border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-950" name="status">
                <option value="">Tất cả trạng thái</option>
                @foreach ($statuses as $status => $label)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ $label }}</option>
                @endforeach
            </select>
            <button class="rounded-md border border-zinc-300 px-4 py-2 text-sm font-medium hover:bg-zinc-50 dark:border-zinc-700" type="submit">Lọc</button>
        </form>

        <div class="overflow-hidden rounded-lg border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-zinc-200 bg-zinc-50 text-xs uppercase text-zinc-500 dark:border-zinc-700 dark:bg-zinc-800">
                    <tr>
                        <th class="px-4 py-3">Mã đơn</th>
                        <th class="px-4 py-3">Khách hàng</th>
                        <th class="px-4 py-3">Ngày đặt</th>
                        <th class="px-4 py-3">Tổng tiền</th>
                        <th class="px-4 py-3">Trạng thái</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($orders as $order)
                        <tr>
                            <td class="px-4 py-3 font-medium">{{ $order->order_code }}</td>
                            <td class="px-4 py-3">{{ $order->user?->name }}<div class="text-xs text-zinc-500">{{ $order->user?->email }}</div></td>
                            <td class="px-4 py-3">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3">{{ number_format((float) $order->total_amount, 0, ',', '.') }} đ</td>
                            <td class="px-4 py-3"><x-status-badge :status="$order->status" /></td>
                            <td class="px-4 py-3 text-right">
                                <a class="rounded-md border border-zinc-300 px-3 py-2 text-xs font-medium hover:bg-zinc-50 dark:border-zinc-700" href="{{ route('admin.orders.show', $order) }}">Xem</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-zinc-500">Chưa có đơn hàng.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $orders->links() }}
    </div>
</x-layouts::app>
