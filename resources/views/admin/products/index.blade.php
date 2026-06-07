<x-layouts::app title="Quản lý sản phẩm">
    @include('partials.flash')

    <div class="space-y-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-semibold">Sản phẩm</h1>
                <p class="mt-1 text-sm text-zinc-500">Quản lý thông tin, giá bán, hình ảnh và trạng thái sản phẩm.</p>
            </div>
            <a class="rounded-md bg-zinc-900 px-4 py-2 text-sm font-medium text-white hover:bg-zinc-800" href="{{ route('admin.products.create') }}">Thêm sản phẩm</a>
        </div>

        <form class="grid gap-3 rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900 md:grid-cols-4" method="GET">
            <input class="rounded-md border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-950" name="search" value="{{ request('search') }}" placeholder="Tìm sản phẩm">
            <select class="rounded-md border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-950" name="category_id">
                <option value="">Tất cả danh mục</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
            <select class="rounded-md border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-950" name="status">
                <option value="">Tất cả trạng thái</option>
                <option value="active" @selected(request('status') === 'active')>Đang bán</option>
                <option value="inactive" @selected(request('status') === 'inactive')>Đang ẩn</option>
                <option value="low_stock" @selected(request('status') === 'low_stock')>Sắp hết hàng</option>
                <option value="out_of_stock" @selected(request('status') === 'out_of_stock')>Hết hàng</option>
            </select>
            <button class="rounded-md border border-zinc-300 px-4 py-2 text-sm font-medium hover:bg-zinc-50 dark:border-zinc-700" type="submit">Lọc</button>
        </form>

        <div class="overflow-hidden rounded-lg border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-zinc-200 bg-zinc-50 text-xs uppercase text-zinc-500 dark:border-zinc-700 dark:bg-zinc-800">
                    <tr>
                        <th class="px-4 py-3">Sản phẩm</th>
                        <th class="px-4 py-3">Danh mục</th>
                        <th class="px-4 py-3">Giá</th>
                        <th class="px-4 py-3">Kho</th>
                        <th class="px-4 py-3">Trạng thái</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($products as $product)
                        <tr>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <img class="size-12 rounded-md object-cover" src="{{ $product->imageUrl() }}" alt="{{ $product->name }}">
                                    <div>
                                        <div class="font-medium">{{ $product->name }}</div>
                                        <div class="text-xs text-zinc-500">SKU {{ $product->sku }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">{{ $product->category?->name }}</td>
                            <td class="px-4 py-3">{{ number_format((float) $product->price, 0, ',', '.') }} đ</td>
                            <td class="px-4 py-3">
                                <span class="{{ $product->stock_quantity === 0 ? 'text-rose-600' : ($product->isLowStock() ? 'text-amber-600' : '') }}">{{ $product->stock_quantity }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="rounded-md px-2 py-1 text-xs font-medium {{ $product->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-zinc-100 text-zinc-600' }}">
                                    {{ $product->is_active ? 'Đang bán' : 'Đang ẩn' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    <a class="rounded-md border border-zinc-300 px-3 py-2 text-xs font-medium hover:bg-zinc-50 dark:border-zinc-700" href="{{ route('admin.products.edit', $product) }}">Sửa</a>
                                    <form method="POST" action="{{ route('admin.products.destroy', $product) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="rounded-md border border-rose-200 px-3 py-2 text-xs font-medium text-rose-700 hover:bg-rose-50" type="submit">Xóa</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-zinc-500">Chưa có sản phẩm.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $products->links() }}
    </div>
</x-layouts::app>
