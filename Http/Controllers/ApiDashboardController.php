<?php

namespace App\Http\Controllers;

use App\Models\InventoryTransaction;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class ApiDashboardController extends Controller
{
    /**
     * GET /api/dashboard/stats
     * Returns summary statistics for the dashboard.
     */
    public function stats(): JsonResponse
    {
        return response()->json([
            'total_products'   => Product::count(),
            'low_stock_items'  => Product::lowStock()->count(),
            'out_of_stock'     => Product::outOfStock()->count(),
            'inventory_value'  => (float) (Product::selectRaw('SUM(quantity * unit_price) as total')->value('total') ?? 0),
            'updated_at'       => now()->toDateTimeString(),
        ]);
    }

    /**
     * GET /api/products/low-stock
     * Returns products at or below reorder level.
     */
    public function lowStock(): JsonResponse
    {
        $products = Product::lowStock()
            ->orWhere(fn ($q) => $q->outOfStock())
            ->orderBy('quantity')
            ->get(['id', 'name', 'sku', 'category', 'quantity', 'reorder_level']);

        return response()->json($products);
    }

    /**
     * GET /api/transactions/recent
     * Returns the 10 most recent transactions.
     */
    public function recentTransactions(): JsonResponse
    {
        $transactions = InventoryTransaction::with(['product:id,name,sku', 'user:id,name'])
            ->recent()
            ->take(10)
            ->get();

        return response()->json($transactions);
    }
}
