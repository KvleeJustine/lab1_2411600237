@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="px-2">
    <div class="d-flex align-items-center mb-3">
        <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-secondary me-3">
            <i class="bi bi-arrow-left"></i> Back to Inventory
        </a>
        <h4 class="mb-0 fw-bold" style="color: var(--dark-green);">
            <i class="bi bi-box-seam me-1"></i> {{ $product->name }}
        </h4>
        <span class="{{ $product->stockBadgeClass() }} ms-3">{{ $product->stockStatus() }}</span>
    </div>

    <div class="row g-3">

        {{-- Product Details Card --}}
        <div class="col-md-7">
            <div class="card h-100">
                <div class="card-header py-2 px-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Product Details</h6>
                    <div class="btn-group btn-group-sm">
                        <a href="{{ route('products.edit', $product) }}" class="btn btn-outline-primary">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <button type="button" class="btn btn-outline-danger"
                                data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="bi bi-trash"></i> Delete
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="detail-label">SKU</div>
                            <div class="detail-value"><code>{{ $product->sku }}</code></div>
                        </div>
                        <div class="col-6">
                            <div class="detail-label">Category</div>
                            <div class="detail-value">{{ $product->category }}</div>
                        </div>
                        <div class="col-6">
                            <div class="detail-label">Quantity in Stock</div>
                            <div class="detail-value fw-bold fs-4"
                                 style="color: {{ $product->isOutOfStock() ? 'var(--accent-red)' : ($product->isLowStock() ? '#d97706' : 'var(--primary)') }}">
                                {{ $product->quantity }}
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="detail-label">Reorder Level</div>
                            <div class="detail-value">{{ $product->reorder_level }}</div>
                        </div>
                        <div class="col-6">
                            <div class="detail-label">Unit Price</div>
                            <div class="detail-value fw-bold">₱{{ number_format($product->unit_price, 2) }}</div>
                        </div>
                        <div class="col-6">
                            <div class="detail-label">Total Stock Value</div>
                            <div class="detail-value fw-bold" style="color:var(--primary);">
                                ₱{{ number_format($product->quantity * $product->unit_price, 2) }}
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="detail-label">Supplier</div>
                            <div class="detail-value">{{ $product->supplier ?? '—' }}</div>
                        </div>
                        <div class="col-12">
                            <div class="detail-label">Description</div>
                            <div class="detail-value">{{ $product->description ?? 'No description provided.' }}</div>
                        </div>
                        <div class="col-6">
                            <div class="detail-label">Date Added</div>
                            <div class="detail-value small">{{ $product->created_at->format('M d, Y') }}</div>
                        </div>
                        <div class="col-6">
                            <div class="detail-label">Last Updated</div>
                            <div class="detail-value small">{{ $product->updated_at->format('M d, Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column --}}
        <div class="col-md-5">

            {{-- Stock Level Card --}}
            <div class="card mb-3">
                <div class="card-header py-2 px-3">
                    <h6 class="mb-0"><i class="bi bi-bar-chart-fill me-1"></i> Stock Level</h6>
                </div>
                <div class="card-body">
                    @php
                        $maxDisplay = max($product->quantity, $product->reorder_level * 3, 10);
                        $pct        = min(round(($product->quantity / $maxDisplay) * 100), 100);
                        $barColor   = $product->isOutOfStock() ? '#dc3545' : ($product->isLowStock() ? '#f3a712' : '#198754');
                    @endphp
                    <div class="d-flex justify-content-between mb-1 small">
                        <span>Current: <strong>{{ $product->quantity }}</strong></span>
                        <span>Reorder at: <strong>{{ $product->reorder_level }}</strong></span>
                    </div>
                    <div class="progress mb-2" style="height: 18px; border-radius: 20px;">
                        <div class="progress-bar" role="progressbar"
                             style="width: {{ $pct }}%; background-color: {{ $barColor }}; border-radius: 20px;">
                            {{ $product->quantity }}
                        </div>
                    </div>
                    <div class="text-center mt-2">
                        <span class="{{ $product->stockBadgeClass() }} px-3 py-1">{{ $product->stockStatus() }}</span>
                    </div>
                    @if ($product->isLowStock() || $product->isOutOfStock())
                        <div class="alert alert-warning mt-3 py-2 mb-0 small">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            @if ($product->isOutOfStock())
                                This item is <strong>out of stock</strong>. Reorder immediately.
                            @else
                                Stock is <strong>low</strong>. Consider reordering soon.
                            @endif
                        </div>
                    @endif
                    <div class="mt-3">
                        <a href="{{ route('transactions.create') }}?product_id={{ $product->id }}"
                           class="btn btn-sm btn-primary-custom w-100">
                            <i class="bi bi-plus-circle me-1"></i> Record Transaction
                        </a>
                    </div>
                </div>
            </div>

            {{-- Recent Transactions --}}
            @if ($transactions->count() > 0)
            <div class="card">
                <div class="card-header py-2 px-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-clock-history me-1"></i> Recent Transactions</h6>
                    <a href="{{ route('transactions.index', ['product_id' => $product->id]) }}"
                       class="btn btn-sm btn-outline-secondary" style="font-size:0.75rem;">View All</a>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr class="table-header" style="font-size:0.75rem;">
                                <th>Date</th>
                                <th>Type</th>
                                <th>Qty</th>
                                <th>Ref</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transactions as $tx)
                            <tr>
                                <td class="small">{{ $tx->created_at->format('M d') }}</td>
                                <td>
                                    <span class="badge {{ $tx->type === 'in' ? 'bg-success' : ($tx->type === 'out' ? 'bg-danger' : 'bg-secondary') }}">
                                        {{ strtoupper($tx->type) }}
                                    </span>
                                </td>
                                <td class="{{ $tx->type === 'in' ? 'text-success' : ($tx->type === 'out' ? 'text-danger' : '') }} fw-bold">
                                    {{ $tx->type === 'in' ? '+' : ($tx->type === 'out' ? '-' : '±') }}{{ $tx->quantity }}
                                </td>
                                <td class="small text-muted">{{ $tx->reference ?? '—' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Delete Modal --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header modal-header-custom">
                <h5 class="modal-title"><i class="bi bi-exclamation-triangle me-2"></i>Delete Product?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-1">You are about to permanently delete:</p>
                <p class="fw-bold" style="color: var(--dark-green);">{{ $product->name }}</p>
                <p class="text-danger small mb-0">
                    <i class="bi bi-exclamation-circle"></i> This action cannot be undone.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('products.destroy', $product) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i> Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
