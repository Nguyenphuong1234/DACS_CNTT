<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $cart = Cart::query()
            ->firstOrCreate(['user_id' => $request->user()->id])
            ->load('items.product.primaryImage');

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng đang trống.');
        }

        $addresses = $request->user()->addresses()->latest('is_default')->latest()->get();

        return view('customer.checkout', compact('cart', 'addresses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'address_id' => ['nullable', 'integer', 'exists:user_addresses,id'],
            'recipient_name' => ['required_without:address_id', 'nullable', 'string', 'max:255'],
            'recipient_phone' => ['required_without:address_id', 'nullable', 'string', 'max:30'],
            'address_line' => ['required_without:address_id', 'nullable', 'string', 'max:255'],
            'ward' => ['nullable', 'string', 'max:255'],
            'district' => ['nullable', 'string', 'max:255'],
            'city' => ['required_without:address_id', 'nullable', 'string', 'max:255'],
            'customer_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $order = DB::transaction(function () use ($request, $validated): Order {
            $cart = Cart::query()
                ->where('user_id', $request->user()->id)
                ->with('items.product')
                ->lockForUpdate()
                ->firstOrFail();

            if ($cart->items->isEmpty()) {
                abort(422, 'Giỏ hàng đang trống.');
            }

            foreach ($cart->items as $item) {
                if (! $item->product->is_active || $item->quantity > $item->product->stock_quantity) {
                    abort(422, 'Một số sản phẩm trong giỏ hàng đã hết hàng hoặc vượt tồn kho.');
                }
            }

            $shipping = $this->shippingInfo($request, $validated);
            $subtotal = $cart->items->sum(fn ($item): float => (float) $item->product->price * $item->quantity);

            $order = Order::query()->create([
                'user_id' => $request->user()->id,
                'order_code' => $this->nextOrderCode(),
                'status' => Order::PENDING,
                'payment_method' => 'cod',
                'recipient_name' => $shipping['recipient_name'],
                'recipient_phone' => $shipping['recipient_phone'],
                'shipping_address' => $shipping['shipping_address'],
                'subtotal' => $subtotal,
                'total_amount' => $subtotal,
                'customer_note' => $validated['customer_note'] ?? null,
            ]);

            foreach ($cart->items as $item) {
                $product = Product::query()->lockForUpdate()->findOrFail($item->product_id);

                if ($item->quantity > $product->stock_quantity) {
                    abort(422, 'Sản phẩm '.$product->name.' không đủ tồn kho.');
                }

                $lineTotal = (float) $product->price * $item->quantity;

                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'unit_price' => $product->price,
                    'quantity' => $item->quantity,
                    'line_total' => $lineTotal,
                ]);

                $product->decrement('stock_quantity', $item->quantity);
            }

            OrderStatusHistory::query()->create([
                'order_id' => $order->id,
                'changed_by' => null,
                'from_status' => null,
                'to_status' => Order::PENDING,
                'note' => 'Khách hàng tạo đơn hàng.',
            ]);

            $cart->items()->delete();

            return $order;
        });

        return redirect()->route('orders.show', $order)->with('success', 'Đặt hàng thành công.');
    }

    private function shippingInfo(Request $request, array $validated): array
    {
        if (! empty($validated['address_id'])) {
            $address = $request->user()->addresses()->findOrFail($validated['address_id']);

            return [
                'recipient_name' => $address->recipient_name,
                'recipient_phone' => $address->recipient_phone,
                'shipping_address' => $address->fullAddress(),
            ];
        }

        return [
            'recipient_name' => $validated['recipient_name'],
            'recipient_phone' => $validated['recipient_phone'],
            'shipping_address' => collect([
                $validated['address_line'],
                $validated['ward'] ?? null,
                $validated['district'] ?? null,
                $validated['city'],
            ])->filter()->implode(', '),
        ];
    }

    private function nextOrderCode(): string
    {
        return 'DH'.now()->format('YmdHis').random_int(100, 999);
    }
}
