<?php

namespace App\Http\Controllers;

use App\Models\InventoryTransaction;
use App\Models\Product;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Main reports page — shows three report types:
     *  1. Stock Movement Report  (transactions in a date range)
     *  2. Low Stock Report       (products at/below reorder level)
     *  3. Inventory Valuation    (total value per category + grand total)
     */
    public function index(Request $request)
    {
        // ── 1. Stock Movement Report ─────────────────────────────────────────
        $movementQuery = InventoryTransaction::with(['product', 'user'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('date_from')) {
            $movementQuery->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $movementQuery->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('movement_type') && $request->movement_type !== 'all') {
            $movementQuery->where('type', $request->movement_type);
        }

        $movements = $movementQuery->paginate(15, ['*'], 'movement_page')
                                   ->withQueryString();

        // ── 2. Low Stock Report ──────────────────────────────────────────────
        $lowStockProducts = Product::where(function ($q) {
            $q->lowStock()->orWhere(fn ($q2) => $q2->outOfStock());
        })->orderBy('quantity')->get();

        // ── 3. Inventory Valuation Report ────────────────────────────────────
        $valuationByCategory = Product::selectRaw(
            'category,
             COUNT(*) as product_count,
             SUM(quantity) as total_units,
             SUM(quantity * unit_price) as total_value'
        )->groupBy('category')
         ->orderBy('category')
         ->get();

        $grandTotalValue = $valuationByCategory->sum('total_value');
        $grandTotalUnits = $valuationByCategory->sum('total_units');

        return view('reports.index', compact(
            'movements',
            'lowStockProducts',
            'valuationByCategory',
            'grandTotalValue',
            'grandTotalUnits'
        ));
    }
}
