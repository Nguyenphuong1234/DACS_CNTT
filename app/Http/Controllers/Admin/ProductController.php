<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::query()->orderBy('name')->get();

        $products = Product::query()
            ->with(['category', 'primaryImage'])
            ->when($request->filled('search'), fn ($query) => $query->where('name', 'like', '%'.$request->string('search')->trim().'%'))
            ->when($request->filled('category_id'), fn ($query) => $query->where('category_id', $request->integer('category_id')))
            ->when($request->filled('status'), function ($query) use ($request): void {
                match ($request->string('status')->toString()) {
                    'active' => $query->where('is_active', true),
                    'inactive' => $query->where('is_active', false),
                    'out_of_stock' => $query->where('stock_quantity', 0),
                    'low_stock' => $query->whereColumn('stock_quantity', '<=', 'low_stock_threshold')->where('stock_quantity', '>', 0),
                    default => null,
                };
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::query()->where('is_active', true)->orderBy('name')->get();

        return view('admin.products.form', ['product' => new Product, 'categories' => $categories]);
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);
        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active');

        $product = Product::query()->create($validated);
        $this->syncImages($request, $product);

        return redirect()->route('admin.products.index')->with('success', 'Đã tạo sản phẩm.');
    }

    public function edit(Product $product)
    {
        $categories = Category::query()->orderBy('name')->get();
        $product->load('images');

        return view('admin.products.form', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $this->validated($request, $product);
        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active');

        $product->update($validated);
        $this->syncImages($request, $product);

        if ($request->filled('primary_image_id')) {
            $product->images()->update(['is_primary' => false]);
            $product->images()->whereKey($request->integer('primary_image_id'))->update(['is_primary' => true]);
        }

        return redirect()->route('admin.products.index')->with('success', 'Đã cập nhật sản phẩm.');
    }

    public function destroy(Product $product)
    {
        foreach ($product->images as $image) {
            if (! str_starts_with($image->path, 'http://') && ! str_starts_with($image->path, 'https://')) {
                Storage::disk('public')->delete($image->path);
            }
        }

        $product->delete();

        return back()->with('success', 'Đã xóa sản phẩm.');
    }

    public function destroyImage(Product $product, ProductImage $image)
    {
        abort_unless($image->product_id === $product->id, 404);

        if (! str_starts_with($image->path, 'http://') && ! str_starts_with($image->path, 'https://')) {
            Storage::disk('public')->delete($image->path);
        }

        $wasPrimary = $image->is_primary;
        $image->delete();

        if ($wasPrimary && $product->images()->exists()) {
            $product->images()->oldest('sort_order')->first()->update(['is_primary' => true]);
        }

        return back()->with('success', 'Đã xóa ảnh sản phẩm.');
    }

    private function validated(Request $request, ?Product $product = null): array
    {
        return $request->validate([
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255', Rule::unique('products', 'name')->ignore($product)],
            'sku' => ['required', 'string', 'max:100', Rule::unique('products', 'sku')->ignore($product)],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'low_stock_threshold' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'images.*' => ['nullable', 'image', 'max:2048'],
            'image_url' => ['nullable', 'url', 'max:1000'],
            'primary_image_id' => ['nullable', 'integer', 'exists:product_images,id'],
        ]);
    }

    private function syncImages(Request $request, Product $product): void
    {
        $hasPrimary = $product->images()->where('is_primary', true)->exists();

        if ($request->filled('image_url')) {
            $product->images()->create([
                'path' => $request->input('image_url'),
                'alt_text' => $product->name,
                'is_primary' => ! $hasPrimary,
                'sort_order' => $product->images()->count(),
            ]);

            $hasPrimary = true;
        }

        foreach ($request->file('images', []) as $index => $file) {
            $path = $file->store('products', 'public');

            $product->images()->create([
                'path' => $path,
                'alt_text' => $product->name,
                'is_primary' => ! $hasPrimary && $index === 0,
                'sort_order' => $product->images()->count() + $index,
            ]);
        }
    }
}
