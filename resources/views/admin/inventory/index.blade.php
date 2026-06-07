<x-layouts::app title="Quản lý kho hàng">
    @include('partials.flash')

    <div class="space-y-5">
        <div>
            <h1 class="text-2xl font-semibold">Kho hàng</h1>
            <p class="mt-1 text-sm text-zinc-500">Theo dõi sản phẩm còn hàng, hết hàng và sắp hết hàng.</p>
        </div>

        <form class="grid gap-3 rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900 md:grid-cols-3" method="GET">
            <input class="rounded-md border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-950" name="search" value="{{ request('search') }}" placeholder="Tìm sản phẩm">
            <select class="rounded-md border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-950" name="filter">
                <option value="">Tất cả</option>
                <option value="low" @selected(request('filter') === 'low')>Sắp hết hàng</option>
                <option value="out" @selected(request('filter') === 'out')>Hết hàng</option>
            </select>
            <button class="rounded-md border border-zinc-300 px-4 py-2 text-sm font-medium hover:bg-zinc-50 dark:border-zinc-700" type="submit">Lọc</button>
        </form>

        <div class="overflow-hidden rounded-lg border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-zinc-200 bg-zinc-50 text-xs uppercase text-zinc-500 dark:border-zinc-700 dark:bg-zinc-800">
                    <tr>
                        <th class="px-4 py-3">Sản phẩm</th>
                        <th class="px-4 py-3">Danh mục</th>
                        <th class="px-4 py-3">Trạng thái kho</th>
                        <th class="px-4 py-3">Cập nhật</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @foreach ($products as $product)
                        <tr>
                            <td class="px-4 py-3 font-medium">{{ $product->name }}</td>
                            <td class="px-4 py-3">{{ $product->category?->name }}</td>
                            <td class="px-4 py-3">
                                @if ($product->stock_quantity === 0)
                                    <span class="rounded-md bg-rose-50 px-2 py-1 text-xs font-medium text-rose-700">Hết hàng</span>
                                @elseif ($product->isLowStock())
                                    <span class="rounded-md bg-amber-50 px-2 py-1 text-xs font-medium text-amber-700">Sắp hết hàng</span>
                                @else
                                    <span class="rounded-md bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700">Còn hàng</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <form class="flex flex-wrap items-center gap-2" method="POST" action="{{ route('admin.inventory.update', $product) }}">
                                    @csrf
                                    @method('PUT')
                                    <input class="w-24 rounded-md border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-950" name="stock_quantity" type="number" min="0" value="{{ $product->stock_quantity }}">
                                    <input class="w-24 rounded-md border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-950" name="low_stock_threshold" type="number" min="0" value="{{ $product->low_stock_threshold }}">
                                    <button class="rounded-md border border-zinc-300 px-3 py-2 text-xs font-medium hover:bg-zinc-50 dark:border-zinc-700" type="submit">Lưu</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $products->links() }}
    </div>
</x-layouts::app>
