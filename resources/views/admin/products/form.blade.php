<x-layouts::app :title="$product->exists ? 'Cập nhật sản phẩm' : 'Thêm sản phẩm'">
    @include('partials.flash')

    <div class="space-y-5">
        <div>
            <h1 class="text-2xl font-semibold">{{ $product->exists ? 'Cập nhật sản phẩm' : 'Thêm sản phẩm' }}</h1>
            <p class="mt-1 text-sm text-zinc-500">Cấu hình thông tin bán hàng, tồn kho và ảnh sản phẩm.</p>
        </div>

        <form class="grid gap-5 rounded-lg border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900 lg:grid-cols-2" method="POST" enctype="multipart/form-data" action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}">
            @csrf
            @if ($product->exists)
                @method('PUT')
            @endif

            <div>
                <label class="mb-1 block text-sm font-medium">Tên sản phẩm</label>
                <input class="w-full rounded-md border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-950" name="name" value="{{ old('name', $product->name) }}" required>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">SKU</label>
                <input class="w-full rounded-md border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-950" name="sku" value="{{ old('sku', $product->sku) }}" required>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">Danh mục</label>
                <select class="w-full rounded-md border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-950" name="category_id" required>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">Giá bán</label>
                <input class="w-full rounded-md border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-950" name="price" type="number" min="0" step="1000" value="{{ old('price', $product->price) }}" required>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">Tồn kho</label>
                <input class="w-full rounded-md border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-950" name="stock_quantity" type="number" min="0" value="{{ old('stock_quantity', $product->stock_quantity ?? 0) }}" required>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">Ngưỡng sắp hết hàng</label>
                <input class="w-full rounded-md border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-950" name="low_stock_threshold" type="number" min="0" value="{{ old('low_stock_threshold', $product->low_stock_threshold ?? 5) }}" required>
            </div>
            <div class="lg:col-span-2">
                <label class="mb-1 block text-sm font-medium">Mô tả</label>
                <textarea class="w-full rounded-md border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-950" name="description" rows="5">{{ old('description', $product->description) }}</textarea>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">Upload ảnh</label>
                <input class="w-full rounded-md border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-950" name="images[]" type="file" accept="image/*" multiple>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">Hoặc URL ảnh</label>
                <input class="w-full rounded-md border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-950" name="image_url" type="url" placeholder="https://...">
            </div>

            @if ($product->exists && $product->images->isNotEmpty())
                <div class="lg:col-span-2">
                    <label class="mb-2 block text-sm font-medium">Ảnh đại diện</label>
                    <div class="grid gap-3 sm:grid-cols-3 lg:grid-cols-4">
                        @foreach ($product->images as $image)
                            <label class="rounded-md border border-zinc-200 p-2 dark:border-zinc-700">
                                <img class="aspect-square w-full rounded object-cover" src="{{ $image->url() }}" alt="{{ $image->alt_text ?? $product->name }}">
                                <span class="mt-2 flex items-center gap-2 text-sm">
                                    <input type="radio" name="primary_image_id" value="{{ $image->id }}" @checked($image->is_primary)>
                                    Đặt đại diện
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endif

            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->exists ? $product->is_active : true))>
                Hiển thị sản phẩm
            </label>

            <div class="flex gap-2 lg:col-span-2">
                <button class="rounded-md bg-zinc-900 px-4 py-2 text-sm font-medium text-white hover:bg-zinc-800" type="submit">Lưu sản phẩm</button>
                <a class="rounded-md border border-zinc-300 px-4 py-2 text-sm font-medium hover:bg-zinc-50 dark:border-zinc-700" href="{{ route('admin.products.index') }}">Quay lại</a>
            </div>
        </form>

        @if ($product->exists && $product->images->isNotEmpty())
            <section class="rounded-lg border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                <h2 class="text-lg font-semibold">Xóa ảnh sản phẩm</h2>
                <div class="mt-4 grid gap-3 sm:grid-cols-4 lg:grid-cols-6">
                    @foreach ($product->images as $image)
                        <form method="POST" action="{{ route('admin.products.images.destroy', [$product, $image]) }}">
                            @csrf
                            @method('DELETE')
                            <img class="aspect-square w-full rounded-md object-cover" src="{{ $image->url() }}" alt="{{ $image->alt_text ?? $product->name }}">
                            <button class="mt-2 w-full rounded-md border border-rose-200 px-3 py-2 text-xs font-medium text-rose-700 hover:bg-rose-50" type="submit">Xóa</button>
                        </form>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</x-layouts::app>
