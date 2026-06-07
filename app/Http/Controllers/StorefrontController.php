<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class StorefrontController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::query()->where('is_active', true)->orderBy('name')->get();

        $products = Product::query()
            ->with(['category', 'primaryImage'])
            ->active()
            ->when($request->filled('search'), function ($query) use ($request): void {
                $query->where('name', 'like', '%'.$request->string('search')->trim().'%');
            })
            ->when($request->filled('category'), function ($query) use ($request): void {
                $query->whereHas('category', fn ($category) => $category->where('slug', $request->string('category')));
            })
            ->when($request->filled('min_price'), function ($query) use ($request): void {
                $query->where('price', '>=', (float) $request->input('min_price'));
            })
            ->when($request->filled('max_price'), function ($query) use ($request): void {
                $query->where('price', '<=', (float) $request->input('max_price'));
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('store.index', compact('categories', 'products'));
    }

    public function show(Product $product)
    {
        abort_unless($product->is_active, 404);

        $product->load(['category', 'images']);

        $relatedProducts = Product::query()
            ->with(['category', 'primaryImage'])
            ->active()
            ->where('category_id', $product->category_id)
            ->whereKeyNot($product->id)
            ->limit(4)
            ->get();

        return view('store.show', compact('product', 'relatedProducts'));
    }
}
