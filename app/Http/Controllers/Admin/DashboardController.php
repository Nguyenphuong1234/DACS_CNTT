<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'products' => Product::query()->count(),
            'orders' => Order::query()->count(),
            'users' => User::query()->count(),
            'revenue' => Order::query()->where('status', Order::COMPLETED)->sum('total_amount'),
        ];

        $latestOrders = Order::query()->with('user')->latest()->limit(8)->get();
        $lowStockProducts = Product::query()
            ->with('category')
            ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->orderBy('stock_quantity')
            ->limit(8)
            ->get();

        $ordersByStatus = Order::query()
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $revenueByDay = Order::query()
            ->where('status', Order::COMPLETED)
            ->whereDate('created_at', '>=', now()->subDays(6))
            ->selectRaw('DATE(created_at) as day, SUM(total_amount) as revenue')
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $bestSellingProducts = OrderItem::query()
            ->select('product_name', DB::raw('SUM(quantity) as sold'))
            ->groupBy('product_name')
            ->orderByDesc('sold')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'latestOrders',
            'lowStockProducts',
            'ordersByStatus',
            'revenueByDay',
            'bestSellingProducts',
        ));
    }
}
