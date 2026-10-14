@extends('layouts.app')

@section('title', 'Transactions')

@section('content')
<div class="px-2">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0 fw-bold" style="color: var(--dark-green);">
                <i class="bi bi-arrow-left-right me-1"></i> Inventory Transactions
            </h4>
            <small class="text-muted">{{ $transactions->total() }} transaction(s) total</small>
        </div>
        <a href="{{ route('transactions.create') }}" class="btn btn-primary-custom">
            <i class="bi bi-plus-circle"></i> Record Transaction
        </a>
    </div>

    {{-- Filters --}}
    <div class="card mb-3">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('transactions.index') }}" class="row g-2 align-items-end">
                <div class="col-12 col-md-3">
                    <label class="form-label small fw-semibold mb-1">Product</label>
                    <select name="product_id" class="form-select form-select-sm">
                        <option value="">All Products</option>
                        @foreach ($products as $p)
                            <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small fw-semibold mb-1">Type</label>
                    <select name="type" class="form-select form-select-sm">
                        <option value="">All Types</option>
                        <option value="in"         {{ request('type') === 'in'         ? 'selected' : '' }}>Stock In</option>
                        <option value="out"        {{ request('type') === 'out'        ? 'selected' : '' }}>Stock Out</option>
                        <option value="adjustment" {{ request('type') === 'adjustment' ? 'selected' : '' }}>Adjustment</option>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small fw-semibold mb-1">Date From</label>
                    <input type="date" name="date_from" class="form-control form-control-sm"
                           value="{{ request('date_from') }}">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small fw-semibold mb-1">Date To</label>
                    <input type="date" name="date_to" class="form-control form-control-sm"
                           value="{{ request('date_to') }}">
                </div>
                <div class="col-12 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary-custom btn-sm flex-fill">
                        <i class="bi bi-search"></i> Filter
                    </button>
                    <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary btn-sm flex-fill">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Transactions Table --}}
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr class="table-header">
                            <th>Date / Time</th>
                            <th>Product</th>
                            <th>Type</th>
                            <th>Qty</th>
                            <th>Reference</th>
                            <th>Recorded By</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions as $tx)
                        <tr>
                            <td class="small text-muted">{{ $tx->created_at->format('M d, Y g:i A') }}</td>
                            <td>
                                <a href="{{ route('products.show', $tx->product_id) }}"
                                   class="fw-semibold text-decoration-none" style="color:var(--dark-green)">
                                    {{ $tx->product->name ?? '—' }}
                                </a>
                                <br>
                                <code class="small text-muted">{{ $tx->product->sku ?? '' }}</code>
                            </td>
                            <td>
                                @if ($tx->type === 'in')
                                    <span class="badge bg-success">Stock In</span>
                                @elseif ($tx->type === 'out')
                                    <span class="badge bg-danger">Stock Out</span>
                                @else
                                    <span class="badge bg-secondary">Adjustment</span>
                                @endif
                            </td>
                            <td>
                                <span class="fw-bold {{ $tx->type === 'in' ? 'text-success' : ($tx->type === 'out' ? 'text-danger' : 'text-secondary') }}">
                                    {{ $tx->type === 'in' ? '+' : ($tx->type === 'out' ? '-' : '±') }}{{ $tx->quantity }}
                                </span>
                            </td>
                            <td class="small">{{ $tx->reference ?? '—' }}</td>
                            <td class="small">{{ $tx->user->name ?? 'System' }}</td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('transactions.show', $tx) }}"
                                       class="btn btn-outline-secondary" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger"
                                            title="Delete"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteModal"
                                            data-tx-id="{{ $tx->id }}"
                                            data-tx-desc="{{ $tx->product->name ?? '' }} ({{ strtoupper($tx->type) }} {{ $tx->quantity }})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-arrow-left-right" style="font-size:2rem;"></i>
                                <p class="mt-2 mb-0">No transactions found.
                                    <a href="{{ route('transactions.create') }}">Record one!</a>
                                </p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($transactions->hasPages())
        <div class="card-footer bg-white d-flex justify-content-between align-items-center">
            <small class="text-muted">
                Showing {{ $transactions->firstItem() }}–{{ $transactions->lastItem() }}
                of {{ $transactions->total() }}
            </small>
            {{ $transactions->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header modal-header-custom">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle me-2"></i>Delete Transaction?
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-1">You are about to delete:</p>
                <p class="fw-bold" id="deleteTxDesc" style="color: var(--dark-green);"></p>
                <div class="alert alert-warning py-2 mb-0 small">
                    <i class="bi bi-info-circle me-1"></i>
                    <strong>Note:</strong> Deleting a transaction will <strong>reverse its stock change</strong>
                    to keep inventory consistent.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i> Delete & Reverse
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.getElementById('deleteModal').addEventListener('show.bs.modal', function (e) {
    const btn  = e.relatedTarget;
    const id   = btn.getAttribute('data-tx-id');
    const desc = btn.getAttribute('data-tx-desc');
    document.getElementById('deleteTxDesc').textContent = desc;
    document.getElementById('deleteForm').action = '/transactions/' + id;
});
</script>
@endsection
