<?php

namespace App\Http\Controllers;

use App\Models\InventoryTransaction;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class InventoryTransactionController extends Controller
{
    private const TYPES = ['in', 'out', 'adjustment'];

    // ── index ────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $query = InventoryTransaction::with(['product', 'user'])->recent();

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('type') && in_array($request->type, self::TYPES)) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->paginate(20)->withQueryString();
        $products     = Product::orderBy('name')->get();

        return view('transactions.index', compact('transactions', 'products'));
    }

    // ── create ───────────────────────────────────────────────────────────────

    public function create()
    {
        $products = Product::orderBy('name')->get();
        $types    = self::TYPES;

        return view('transactions.create', compact('products', 'types'));
    }

    // ── store ────────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id'       => ['required', 'exists:products,id'],
            'type'             => ['required', Rule::in(self::TYPES)],
            'quantity'         => ['required', 'integer', 'min:1'],
            'reference'        => ['nullable', 'string', 'max:100'],
            'notes'            => ['nullable', 'string', 'max:1000'],
        ], [
            'product_id.required' => 'Please select a product.',
            'product_id.exists'   => 'The selected product does not exist.',
            'type.in'             => 'Transaction type must be Stock In, Stock Out, or Adjustment.',
            'quantity.min'        => 'Quantity must be at least 1.',
        ]);

        // Extra check: ensure sufficient stock for outbound transactions
        if ($validated['type'] === 'out') {
            $product = Product::findOrFail($validated['product_id']);
            if ($validated['quantity'] > $product->quantity) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'quantity' => "Insufficient stock. Available: {$product->quantity}, Requested: {$validated['quantity']}.",
                    ]);
            }
        }

        try {
            DB::transaction(function () use ($validated) {
                InventoryTransaction::create(array_merge(
                    $validated,
                    ['user_id' => Auth::id()]
                ));
            });
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('transactions.index')
            ->with('success', 'Transaction recorded successfully.');
    }

    // ── show ─────────────────────────────────────────────────────────────────

    public function show(InventoryTransaction $transaction)
    {
        $transaction->load(['product', 'user']);

        return view('transactions.show', compact('transaction'));
    }

    // ── destroy ──────────────────────────────────────────────────────────────

    /**
     * Deleting a transaction reverses its stock effect so the inventory
     * remains consistent (e.g., deleting a "stock out" adds back to quantity).
     */
    public function destroy(InventoryTransaction $transaction)
    {
        try {
            DB::transaction(function () use ($transaction) {
                $product = Product::lockForUpdate()->findOrFail($transaction->product_id);

                // Reverse the stock change
                if ($transaction->type === 'in') {
                    if ($transaction->quantity > $product->quantity) {
                        throw new \Exception(
                            'Cannot delete: reversing this transaction would result in negative inventory.'
                        );
                    }
                    $product->quantity -= $transaction->quantity;
                } elseif ($transaction->type === 'out') {
                    $product->quantity += $transaction->quantity;
                }
                // adjustments: also reverse
                $product->save();

                $transaction->delete();
            });
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('transactions.index')
            ->with('success', 'Transaction deleted and stock reversed.');
    }
}
