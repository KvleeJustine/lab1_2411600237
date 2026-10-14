<?php

namespace App\Http\Controllers;

use App\Models\InventoryTransaction;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProducts   = Product::count();
        $lowStockItems   = Product::lowStock()->count();
        $outOfStockItems = Product::outOfStock()->count();
        $inventoryValue  = Product::selectRaw('SUM(quantity * unit_price) as total')->value('total') ?? 0;

        // Category breakdown for chart
        $categoryData = Product::selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->orderBy('category')
            ->get();

        // Stock status distribution for chart
        $inStockCount = Product::whereRaw('quantity > reorder_level')->count();
        $statusData   = [
            'In Stock'     => $inStockCount,
            'Low Stock'    => $lowStockItems,
            'Out of Stock' => $outOfStockItems,
        ];

        // Recent products (last 6 added)
        $recentProducts = Product::latest()->take(6)->get();

        // Top 5 most valuable inventory items
        $topValueItems = Product::selectRaw('*, (quantity * unit_price) as total_value')
            ->orderByRaw('quantity * unit_price DESC')
            ->take(5)
            ->get();

        // Recent transactions for dashboard
        $recentTransactions = InventoryTransaction::with(['product', 'user'])
            ->recent()
            ->take(8)
            ->get();

        // Unread notification count for the bell icon
        $unreadNotifications = Auth::check()
            ? Auth::user()->unreadNotifications()->count()
            : 0;

        return view('dashboard', compact(
            'totalProducts',
            'lowStockItems',
            'outOfStockItems',
            'inventoryValue',
            'categoryData',
            'statusData',
            'recentProducts',
            'topValueItems',
            'recentTransactions',
            'unreadNotifications'
        ));
    }
}
