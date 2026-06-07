@extends('layouts.store')

@section('content')
    <section class="mb-8 grid gap-6 lg:grid-cols-[1.2fr_0.8fr] lg:items-end">
        <div>
            <p class="text-sm font-medium uppercase tracking-wide text-zinc-500">Hệ thống bán hàng trực tuyến</p>
            <h1 class="mt-3 max-w-3xl text-3xl font-semibold tracking-normal text-zinc-950 sm:text-4xl">Mua sắm thiết bị công nghệ, quản lý đơn hàng gọn gàng.</h1>
            <p class="mt-4 max-w-2xl text-zinc-600">Tìm kiếm sản phẩm, kiểm tra tồn kho, đặt hàng COD và theo dõi trạng thái xử lý ngay trong tài khoản.</p>
        </div>
        <div class="rounded-lg border border-zinc-200 bg-white p-4">
            <form class="grid gap-3 sm:grid-cols-2" method="GET" action="{{ route('home') }}">
                <input class="rounded-md border border-zinc-300 px-3 py-2 text-sm" name="search" value="{{ request('search') }}" placeholder="Tìm theo tên sản phẩm">
                <select class="rounded-md border border-zinc-300 px-3 py-2 text-sm" name="category">
                    <option value="">Tất cả danh mục</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->slug }}" @selected(request('category') === $category->slug)>{{ $category->name }}</option>
                    @endforeach
                </select>
                <input class="rounded-md border border-zinc-300 px-3 py-2 text-sm" name="min_price" value="{{ request('min_price') }}" type="number" min="0" placeholder="Giá từ">
                <input class="rounded-md border border-zinc-300 px-3 py-2 text-sm" name="max_price" value="{{ request('max_price') }}" type="number" min="0" placeholder="Giá đến">
                <button class="rounded-md bg-zinc-900 px-4 py-2 text-sm font-medium text-white hover:bg-zinc-800" type="submit">Lọc sản phẩm</button>
                <a class="rounded-md border border-zinc-300 px-4 py-2 text-center text-sm font-medium hover:bg-zinc-100" href="{{ route('home') }}">Xóa lọc</a>
            </form>
        </div>
    </section>

    <section class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
        @forelse ($products as $product)
            <article class="overflow-hidden rounded-lg border border-zinc-200 bg-white">
                <a href="{{ route('products.show', $product) }}">
                    <img class="aspect-[4/3] w-full object-cover" src="{{ $product->imageUrl() }}" alt="{{ $product->name }}">
                </a>
                <div class="space-y-3 p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <a class="font-semibold text-zinc-950 hover:underline" href="{{ route('products.show', $product) }}">{{ $product->name }}</a>
                            <div class="mt-1 text-sm text-zinc-500">{{ $product->category?->name }}</div>
                        </div>
                        <span class="rounded-md px-2 py-1 text-xs font-medium {{ $product->isInStock() ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                            {{ $product->isInStock() ? 'Còn hàng' : 'Hết hàng' }}
                        </span>
                    </div>
                    <div class="text-lg font-semibold">{{ number_format((float) $product->price, 0, ',', '.') }} đ</div>
                    <form method="POST" action="{{ route('cart.store', $product) }}" class="flex gap-2">
                        @csrf
                        <input type="hidden" name="quantity" value="1">
                        <button class="flex-1 rounded-md bg-zinc-900 px-3 py-2 text-sm font-medium text-white hover:bg-zinc-800 disabled:cursor-not-allowed disabled:bg-zinc-300" @disabled(! $product->isInStock())>
                            Thêm vào giỏ
                        </button>
                    </form>
                </div>
            </article>
        @empty
            <div class="col-span-full rounded-lg border border-dashed border-zinc-300 bg-white p-8 text-center text-zinc-600">Không tìm thấy sản phẩm phù hợp.</div>
        @endforelse
    </section>

    <div class="mt-8">
        {{ $products->links() }}
    </div>
@endsection
