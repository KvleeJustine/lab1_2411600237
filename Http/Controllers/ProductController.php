<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            switch ($request->status) {
                case 'low':
                    $query->whereRaw('quantity <= reorder_level')->where('quantity', '>', 0);
                    break;
                case 'out':
                    $query->where('quantity', '<=', 0);
                    break;
                case 'in':
                    $query->whereRaw('quantity > reorder_level');
                    break;
            }
        }

        $products   = $query->orderBy('name')->paginate(15)->withQueryString();
        $categories = Product::select('category')->distinct()->orderBy('category')->pluck('category');

        return view('products.index', compact('products', 'categories'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'sku'           => 'required|string|max:100|unique:products,sku',
            'description'   => 'nullable|string|max:1000',
            'category'      => 'required|string|max:100',
            'quantity'      => 'required|integer|min:0',
            'reorder_level' => 'required|integer|min:0',
            'unit_price'    => 'required|numeric|min:0.01',
            'supplier'      => 'nullable|string|max:255',
        ], [
            'sku.unique'        => 'This SKU is already in use. Please choose a different one.',
            'unit_price.min'    => 'Unit price must be at least P0.01.',
            'quantity.min'      => 'Quantity cannot be negative.',
            'reorder_level.min' => 'Reorder level cannot be negative.',
        ]);

        Product::create($validated);

        return redirect()->route('products.index')
            ->with('success', 'Product "' . $validated['name'] . '" added successfully!');
    }

    public function show(Product $product)
    {
        $transactions = $product->transactions()->with('user')->latest()->take(10)->get();
        return view('products.show', compact('product', 'transactions'));
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'sku'           => 'required|string|max:100|unique:products,sku,' . $product->id,
            'description'   => 'nullable|string|max:1000',
            'category'      => 'required|string|max:100',
            'quantity'      => 'required|integer|min:0',
            'reorder_level' => 'required|integer|min:0',
            'unit_price'    => 'required|numeric|min:0.01',
            'supplier'      => 'nullable|string|max:255',
        ], [
            'sku.unique'        => 'This SKU is already in use by another product.',
            'unit_price.min'    => 'Unit price must be at least P0.01.',
            'quantity.min'      => 'Quantity cannot be negative.',
            'reorder_level.min' => 'Reorder level cannot be negative.',
        ]);

        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', 'Product "' . $product->name . '" updated successfully!');
    }

    public function destroy(Product $product)
    {
        $name = $product->name;
        $product->delete();
        return redirect()->route('products.index')
            ->with('success', 'Product "' . $name . '" deleted successfully.');
    }
}
