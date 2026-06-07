<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use Illuminate\Http\Request;

class CustomerOrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = $request->user()
            ->orders()
            ->withCount('items')
            ->latest()
            ->paginate(10);

        return view('customer.orders.index', compact('orders'));
    }

    public function show(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        $order->load(['items', 'histories.changer']);

        return view('customer.orders.show', compact('order'));
    }

    public function cancel(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        if (! in_array($order->status, [Order::PENDING, Order::CONFIRMED], true)) {
            return back()->with('error', 'Đơn hàng này không thể hủy ở trạng thái hiện tại.');
        }

        $fromStatus = $order->status;
        $order->update([
            'status' => Order::CANCELLED,
            'cancelled_at' => now(),
        ]);

        OrderStatusHistory::query()->create([
            'order_id' => $order->id,
            'changed_by' => $request->user()->id,
            'from_status' => $fromStatus,
            'to_status' => Order::CANCELLED,
            'note' => 'Khách hàng yêu cầu hủy đơn.',
        ]);

        return back()->with('success', 'Đã hủy đơn hàng.');
    }
}
