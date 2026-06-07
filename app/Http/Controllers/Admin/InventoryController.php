<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::query()
            ->with('category')
            ->when($request->filled('search'), fn ($query) => $query->where('name', 'like', '%'.$request->string('search')->trim().'%'))
            ->when($request->input('filter') === 'low', fn ($query) => $query->whereColumn('stock_quantity', '<=', 'low_stock_threshold')->where('stock_quantity', '>', 0))
            ->when($request->input('filter') === 'out', fn ($query) => $query->where('stock_quantity', 0))
            ->orderBy('stock_quantity')
            ->paginate(15)
            ->withQueryString();

        return view('admin.inventory.index', compact('products'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'low_stock_threshold' => ['required', 'integer', 'min:0'],
        ]);

        $product->update($validated);

        return back()->with('success', 'Đã cập nhật tồn kho.');
    }
}
