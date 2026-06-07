<x-layouts::app title="Tổng quan quản trị">
    @include('partials.flash')

    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-semibold text-zinc-950 dark:text-white">Tổng quan</h1>
            <p class="mt-1 text-sm text-zinc-500">Theo dõi sản phẩm, đơn hàng, người dùng và doanh thu.</p>
        </div>

        <div class="grid gap-4 md:grid-cols-4">
            @foreach ([['Sản phẩm', $stats['products']], ['Đơn hàng', $stats['orders']], ['Người dùng', $stats['users']], ['Doanh thu', number_format((float) $stats['revenue'], 0, ',', '.').' đ']] as [$label, $value])
                <div class="rounded-lg border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                    <div class="text-sm text-zinc-500">{{ $label }}</div>
                    <div class="mt-2 text-2xl font-semibold text-zinc-950 dark:text-white">{{ $value }}</div>
                </div>
            @endforeach
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <section class="rounded-lg border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                <h2 class="text-lg font-semibold">Đơn hàng mới nhất</h2>
                <div class="mt-4 divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($latestOrders as $order)
                        <a class="flex items-center justify-between gap-4 py-3" href="{{ route('admin.orders.show', $order) }}">
                            <div>
                                <div class="font-medium">{{ $order->order_code }}</div>
                                <div class="text-sm text-zinc-500">{{ $order->user?->name }} · {{ $order->created_at->format('d/m/Y H:i') }}</div>
                            </div>
                            <div class="text-right">
                                <x-status-badge :status="$order->status" />
                                <div class="mt-1 text-sm font-medium">{{ number_format((float) $order->total_amount, 0, ',', '.') }} đ</div>
                            </div>
                        </a>
                    @empty
                        <div class="py-6 text-sm text-zinc-500">Chưa có đơn hàng.</div>
                    @endforelse
                </div>
            </section>

            <section class="rounded-lg border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                <h2 class="text-lg font-semibold">Sản phẩm sắp hết hàng</h2>
                <div class="mt-4 divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($lowStockProducts as $product)
                        <div class="flex items-center justify-between gap-4 py-3">
                            <div>
                                <div class="font-medium">{{ $product->name }}</div>
                                <div class="text-sm text-zinc-500">{{ $product->category?->name }}</div>
                            </div>
                            <div class="text-sm font-semibold {{ $product->stock_quantity === 0 ? 'text-rose-600' : 'text-amber-600' }}">
                                {{ $product->stock_quantity }} tồn
                            </div>
                        </div>
                    @empty
                        <div class="py-6 text-sm text-zinc-500">Chưa có cảnh báo tồn kho.</div>
                    @endforelse
                </div>
            </section>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <section class="rounded-lg border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                <h2 class="text-lg font-semibold">Đơn hàng theo trạng thái</h2>
                <div class="mt-4 space-y-3">
                    @foreach (\App\Models\Order::statuses() as $status => $label)
                        <div class="flex items-center justify-between text-sm">
                            <span>{{ $label }}</span>
                            <span class="font-semibold">{{ $ordersByStatus[$status] ?? 0 }}</span>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="rounded-lg border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                <h2 class="text-lg font-semibold">Doanh thu 7 ngày</h2>
                <div class="mt-4 space-y-3">
                    @forelse ($revenueByDay as $day)
                        <div class="flex items-center justify-between text-sm">
                            <span>{{ \Illuminate\Support\Carbon::parse($day->day)->format('d/m') }}</span>
                            <span class="font-semibold">{{ number_format((float) $day->revenue, 0, ',', '.') }} đ</span>
                        </div>
                    @empty
                        <div class="text-sm text-zinc-500">Chưa có doanh thu hoàn thành.</div>
                    @endforelse
                </div>
            </section>

            <section class="rounded-lg border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                <h2 class="text-lg font-semibold">Sản phẩm bán chạy</h2>
                <div class="mt-4 space-y-3">
                    @forelse ($bestSellingProducts as $item)
                        <div class="flex items-center justify-between gap-4 text-sm">
                            <span class="truncate">{{ $item->product_name }}</span>
                            <span class="font-semibold">{{ $item->sold }}</span>
                        </div>
                    @empty
                        <div class="text-sm text-zinc-500">Chưa có dữ liệu bán hàng.</div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</x-layouts::app>
