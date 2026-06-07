@extends('layouts.store')

@section('content')
    <section class="grid gap-8 lg:grid-cols-[1fr_0.85fr]">
        <div class="space-y-4">
            <img class="aspect-[4/3] w-full rounded-lg border border-zinc-200 object-cover" src="{{ $product->imageUrl() }}" alt="{{ $product->name }}">
            @if ($product->images->count() > 1)
                <div class="grid grid-cols-4 gap-3">
                    @foreach ($product->images as $image)
                        <img class="aspect-square rounded-md border border-zinc-200 object-cover" src="{{ $image->url() }}" alt="{{ $image->alt_text ?? $product->name }}">
                    @endforeach
                </div>
            @endif
        </div>

        <div class="rounded-lg border border-zinc-200 bg-white p-6">
            <div class="text-sm font-medium text-zinc-500">{{ $product->category?->name }} · SKU {{ $product->sku }}</div>
            <h1 class="mt-3 text-3xl font-semibold">{{ $product->name }}</h1>
            <div class="mt-4 text-2xl font-semibold">{{ number_format((float) $product->price, 0, ',', '.') }} đ</div>
            <div class="mt-3 inline-flex rounded-md px-2 py-1 text-sm font-medium {{ $product->isInStock() ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                {{ $product->isInStock() ? 'Còn '.$product->stock_quantity.' sản phẩm' : 'Hết hàng' }}
            </div>
            <p class="mt-6 leading-7 text-zinc-700">{{ $product->description }}</p>

            <form class="mt-6 flex max-w-sm gap-3" method="POST" action="{{ route('cart.store', $product) }}">
                @csrf
                <input class="w-24 rounded-md border border-zinc-300 px-3 py-2 text-sm" type="number" name="quantity" min="1" max="{{ max(1, $product->stock_quantity) }}" value="1">
                <button class="flex-1 rounded-md bg-zinc-900 px-4 py-2 text-sm font-medium text-white hover:bg-zinc-800 disabled:cursor-not-allowed disabled:bg-zinc-300" @disabled(! $product->isInStock())>
                    Thêm vào giỏ hàng
                </button>
            </form>
        </div>
    </section>

    @if ($relatedProducts->isNotEmpty())
        <section class="mt-10">
            <h2 class="mb-4 text-xl font-semibold">Sản phẩm cùng danh mục</h2>
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($relatedProducts as $related)
                    <a class="overflow-hidden rounded-lg border border-zinc-200 bg-white hover:border-zinc-400" href="{{ route('products.show', $related) }}">
                        <img class="aspect-[4/3] w-full object-cover" src="{{ $related->imageUrl() }}" alt="{{ $related->name }}">
                        <div class="p-4">
                            <div class="font-semibold">{{ $related->name }}</div>
                            <div class="mt-1 text-sm text-zinc-500">{{ number_format((float) $related->price, 0, ',', '.') }} đ</div>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif
@endsection
