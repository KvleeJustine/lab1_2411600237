@extends('layouts.app')

@section('title', 'Inventory Reports')

@section('content')
<div class="px-2">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0 fw-bold" style="color: var(--dark-green);">
                <i class="bi bi-file-earmark-bar-graph me-1"></i> Inventory Reports
            </h4>
            <small class="text-muted">Generated on {{ now()->format('F d, Y g:i A') }}</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-speedometer2 me-1"></i> Dashboard
            </a>
            <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left-right me-1"></i> Transactions
            </a>
        </div>
    </div>

    {{-- ================================================================== --}}
    {{-- REPORT 1: STOCK MOVEMENT                                           --}}
    {{-- ================================================================== --}}
    <div class="card mb-4">
        <div class="card-header py-2 px-3">
            <h6 class="mb-0">
                <i class="bi bi-arrow-left-right me-1"></i> Stock Movement Report
            </h6>
        </div>
        <div class="card-body border-bottom pb-3">
            {{-- Filters --}}
            <form method="GET" action="{{ route('reports.index') }}" class="row g-2 align-items-end">
                <div class="col-6 col-md-3">
                    <label class="form-label small fw-semibold mb-1">Date From</label>
                    <input type="date" name="date_from" class="form-control form-control-sm"
                           value="{{ request('date_from') }}">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small fw-semibold mb-1">Date To</label>
                    <input type="date" name="date_to" class="form-control form-control-sm"
                           value="{{ request('date_to') }}">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small fw-semibold mb-1">Type</label>
                    <select name="movement_type" class="form-select form-select-sm">
                        <option value="all">All Types</option>
                        <option value="in"         {{ request('movement_type') === 'in'         ? 'selected' : '' }}>Stock In</option>
                        <option value="out"        {{ request('movement_type') === 'out'        ? 'selected' : '' }}>Stock Out</option>
                        <option value="adjustment" {{ request('movement_type') === 'adjustment' ? 'selected' : '' }}>Adjustment</option>
                    </select>
                </div>
                <div class="col-6 col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary-custom btn-sm flex-fill">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                    <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm flex-fill">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </a>
                </div>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr class="table-header">
                            <th>Date / Time</th>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Type</th>
                            <th>Qty Change</th>
                            <th>Reference</th>
                            <th>Recorded By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($movements as $tx)
                        <tr>
                            <td class="small text-muted">
                                {{ $tx->created_at->format('M d, Y g:i A') }}
                            </td>
                            <td>
                                <a href="{{ route('products.show', $tx->product_id) }}"
                                   class="text-decoration-none fw-semibold small"
                                   style="color:var(--dark-green)">
                                    {{ $tx->product->name ?? '—' }}
                                </a>
                            </td>
                            <td><code class="small">{{ $tx->product->sku ?? '—' }}</code></td>
                            <td>
                                @if ($tx->type === 'in')
                                    <span class="badge bg-success">Stock In</span>
                                @elseif ($tx->type === 'out')
                                    <span class="badge bg-danger">Stock Out</span>
                                @else
                                    <span class="badge bg-secondary">Adjustment</span>
                                @endif
                            </td>
                            <td class="fw-bold {{ $tx->type === 'in' ? 'text-success' : ($tx->type === 'out' ? 'text-danger' : 'text-secondary') }}">
                                {{ $tx->type === 'in' ? '+' : ($tx->type === 'out' ? '-' : '±') }}{{ $tx->quantity }}
                            </td>
                            <td class="small">{{ $tx->reference ?? '—' }}</td>
                            <td class="small text-muted">{{ $tx->user->name ?? 'System' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox" style="font-size:2rem;"></i>
                                <p class="mt-2 mb-0">No transactions found for the selected filters.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($movements->hasPages())
            <div class="px-3 py-2 border-top d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Showing {{ $movements->firstItem() }}–{{ $movements->lastItem() }}
                    of {{ $movements->total() }} transactions
                </small>
                {{ $movements->links() }}
            </div>
            @endif
        </div>
    </div>

    {{-- ================================================================== --}}
    {{-- REPORT 2: LOW STOCK                                                --}}
    {{-- ================================================================== --}}
    <div class="card mb-4">
        <div class="card-header py-2 px-3 d-flex justify-content-between align-items-center">
            <h6 class="mb-0">
                <i class="bi bi-exclamation-triangle-fill me-1 text-warning"></i> Low Stock Report
            </h6>
            <span class="badge rounded-pill bg-warning text-dark">
                {{ $lowStockProducts->count() }} item(s)
            </span>
        </div>
        <div class="card-body p-0">
            @if ($lowStockProducts->isEmpty())
                <p class="text-center text-muted py-4 mb-0">
                    <i class="bi bi-check-circle text-success me-1"></i>
                    All products are sufficiently stocked.
                </p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr class="table-header">
                                <th>Product</th>
                                <th>SKU</th>
                                <th>Category</th>
                                <th>Current Qty</th>
                                <th>Reorder Level</th>
                                <th>Shortage</th>
                                <th>Status</th>
                                <th>Supplier</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($lowStockProducts as $p)
                            <tr class="{{ $p->isOutOfStock() ? 'out-of-stock-row' : 'low-stock-row' }}">
                                <td>
                                    <a href="{{ route('products.show', $p) }}"
                                       class="fw-semibold text-decoration-none"
                                       style="color:var(--dark-green)">{{ $p->name }}</a>
                                </td>
                                <td><code class="small">{{ $p->sku }}</code></td>
                                <td>{{ $p->category }}</td>
                                <td class="fw-bold {{ $p->isOutOfStock() ? 'text-danger' : 'text-warning' }}">
                                    {{ $p->quantity }}
                                </td>
                                <td>{{ $p->reorder_level }}</td>
                                <td class="text-danger fw-bold">
                                    {{ max(0, $p->reorder_level - $p->quantity) }} units short
                                </td>
                                <td>
                                    <span class="{{ $p->stockBadgeClass() }}">{{ $p->stockStatus() }}</span>
                                </td>
                                <td class="small text-muted">{{ $p->supplier ?? '—' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- ================================================================== --}}
    {{-- REPORT 3: INVENTORY VALUATION                                      --}}
    {{-- ================================================================== --}}
    <div class="card mb-4">
        <div class="card-header py-2 px-3">
            <h6 class="mb-0">
                <i class="bi bi-cash-coin me-1"></i> Inventory Valuation Report
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr class="table-header">
                            <th>Category</th>
                            <th class="text-center">Products</th>
                            <th class="text-center">Total Units</th>
                            <th class="text-end">Total Value (PHP)</th>
                            <th class="text-end">% of Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($valuationByCategory as $row)
                        <tr>
                            <td class="fw-semibold">{{ $row->category }}</td>
                            <td class="text-center">{{ $row->product_count }}</td>
                            <td class="text-center">{{ number_format($row->total_units) }}</td>
                            <td class="text-end fw-bold" style="color:var(--primary);">
                                ₱{{ number_format($row->total_value, 2) }}
                            </td>
                            <td class="text-end">
                                @php
                                    $pct = $grandTotalValue > 0
                                        ? round(($row->total_value / $grandTotalValue) * 100, 1)
                                        : 0;
                                @endphp
                                <div class="d-flex align-items-center justify-content-end gap-2">
                                    <div class="progress flex-grow-1" style="height:8px; max-width:80px;">
                                        <div class="progress-bar"
                                             style="width:{{ $pct }}%; background-color:var(--primary);"></div>
                                    </div>
                                    <span class="small">{{ $pct }}%</span>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr style="background-color: var(--light-accent); font-weight:700;">
                            <td>TOTAL</td>
                            <td class="text-center">—</td>
                            <td class="text-center">{{ number_format($grandTotalUnits) }}</td>
                            <td class="text-end" style="color:var(--dark-green);">
                                ₱{{ number_format($grandTotalValue, 2) }}
                            </td>
                            <td class="text-end">100%</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
