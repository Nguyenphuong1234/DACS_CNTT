<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::query()
            ->with('user')
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = '%'.$request->string('search')->trim().'%';
                $query->where(function ($query) use ($search): void {
                    $query->where('order_code', 'like', $search)
                        ->orWhere('recipient_name', 'like', $search)
                        ->orWhereHas('user', fn ($user) => $user->where('name', 'like', $search)->orWhere('email', 'like', $search));
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $statuses = Order::statuses();

        return view('admin.orders.index', compact('orders', 'statuses'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items', 'histories.changer']);
        $statuses = Order::statuses();

        return view('admin.orders.show', compact('order', 'statuses'));
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(array_keys(Order::statuses()))],
            'admin_note' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($order->status === Order::CANCELLED && $validated['status'] !== Order::CANCELLED) {
            return back()->with('error', 'Không thể mở lại đơn hàng đã hủy.');
        }

        DB::transaction(function () use ($request, $order, $validated): void {
            $fromStatus = $order->status;
            $toStatus = $validated['status'];

            if ($fromStatus === $toStatus && ($order->admin_note ?? '') === ($validated['admin_note'] ?? '')) {
                return;
            }

            if ($toStatus === Order::CANCELLED && $fromStatus !== Order::CANCELLED) {
                $order->load('items.product');

                foreach ($order->items as $item) {
                    $item->product?->increment('stock_quantity', $item->quantity);
                }
            }

            $order->fill([
                'status' => $toStatus,
                'admin_note' => $validated['admin_note'] ?? null,
                'completed_at' => $toStatus === Order::COMPLETED ? now() : $order->completed_at,
                'cancelled_at' => $toStatus === Order::CANCELLED ? now() : $order->cancelled_at,
            ])->save();

            if ($fromStatus !== $toStatus) {
                OrderStatusHistory::query()->create([
                    'order_id' => $order->id,
                    'changed_by' => $request->user()->id,
                    'from_status' => $fromStatus,
                    'to_status' => $toStatus,
                    'note' => $validated['admin_note'] ?? 'Quản trị viên cập nhật trạng thái.',
                ]);
            }
        });

        return back()->with('success', 'Đã cập nhật đơn hàng.');
    }
}
