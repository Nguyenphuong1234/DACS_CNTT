<x-layouts::app :title="$category->exists ? 'Cập nhật danh mục' : 'Thêm danh mục'">
    @include('partials.flash')

    <div class="max-w-2xl">
        <div class="mb-5">
            <h1 class="text-2xl font-semibold">{{ $category->exists ? 'Cập nhật danh mục' : 'Thêm danh mục' }}</h1>
            <p class="mt-1 text-sm text-zinc-500">Tên danh mục sẽ được dùng để lọc sản phẩm ở storefront.</p>
        </div>

        <form class="space-y-4 rounded-lg border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900" method="POST" action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}">
            @csrf
            @if ($category->exists)
                @method('PUT')
            @endif

            <div>
                <label class="mb-1 block text-sm font-medium">Tên danh mục</label>
                <input class="w-full rounded-md border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-950" name="name" value="{{ old('name', $category->name) }}" required>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">Mô tả</label>
                <textarea class="w-full rounded-md border border-zinc-300 px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-950" name="description" rows="4">{{ old('description', $category->description) }}</textarea>
            </div>
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->exists ? $category->is_active : true))>
                Hiển thị danh mục
            </label>

            <div class="flex gap-2">
                <button class="rounded-md bg-zinc-900 px-4 py-2 text-sm font-medium text-white hover:bg-zinc-800" type="submit">Lưu</button>
                <a class="rounded-md border border-zinc-300 px-4 py-2 text-sm font-medium hover:bg-zinc-50 dark:border-zinc-700" href="{{ route('admin.categories.index') }}">Quay lại</a>
            </div>
        </form>
    </div>
</x-layouts::app>
