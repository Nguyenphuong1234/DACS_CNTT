<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cart = $this->cart($request)->load('items.product.primaryImage');

        return view('customer.cart', compact('cart'));
    }

    public function store(Request $request, Product $product)
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        abort_unless($product->is_active, 404);

        $cart = $this->cart($request);
        $item = $cart->items()->firstOrNew(['product_id' => $product->id]);
        $quantity = (int) ($item->exists ? $item->quantity : 0) + (int) $validated['quantity'];

        if ($quantity > $product->stock_quantity) {
            return back()->with('error', 'Số lượng vượt quá tồn kho hiện tại.');
        }

        $item->quantity = $quantity;
        $item->save();

        return redirect()->route('cart.index')->with('success', 'Đã thêm sản phẩm vào giỏ hàng.');
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $cart = $this->cart($request);
        $item = $cart->items()->where('product_id', $product->id)->firstOrFail();

        if ((int) $validated['quantity'] > $product->stock_quantity) {
            return back()->with('error', 'Số lượng vượt quá tồn kho hiện tại.');
        }

        $item->update(['quantity' => (int) $validated['quantity']]);

        return back()->with('success', 'Đã cập nhật giỏ hàng.');
    }

    public function destroy(Request $request, Product $product)
    {
        $this->cart($request)->items()->where('product_id', $product->id)->delete();

        return back()->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng.');
    }

    private function cart(Request $request): Cart
    {
        return Cart::query()->firstOrCreate(['user_id' => $request->user()->id]);
    }
}
