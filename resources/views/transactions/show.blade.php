@extends('layouts.app')

@section('title', 'Transaction #' . $transaction->id)

@section('content')
<div class="px-2">

    <div class="d-flex align-items-center mb-3">
        <a href="{{ route('transactions.index') }}" class="btn btn-sm btn-outline-secondary me-3">
            <i class="bi bi-arrow-left"></i> Back to Transactions
        </a>
        <h4 class="mb-0 fw-bold" style="color: var(--dark-green);">
            <i class="bi bi-receipt me-1"></i> Transaction #{{ $transaction->id }}
        </h4>
        @if ($transaction->type === 'in')
            <span class="badge bg-success ms-3">Stock In</span>
        @elseif ($transaction->type === 'out')
            <span class="badge bg-danger ms-3">Stock Out</span>
        @else
            <span class="badge bg-secondary ms-3">Adjustment</span>
        @endif
    </div>

    <div class="row g-3">

        {{-- Transaction Details --}}
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header py-2 px-3">
                    <h6 class="mb-0"><i class="bi bi-receipt me-1"></i> Transaction Details</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="detail-label">Transaction ID</div>
                        <div class="detail-value fw-bold">#{{ $transaction->id }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="detail-label">Type</div>
                        <div class="detail-value">
                            @if ($transaction->type === 'in')
                                <span class="badge bg-success">Stock In</span>
                            @elseif ($transaction->type === 'out')
                                <span class="badge bg-danger">Stock Out</span>
                            @else
                                <span class="badge bg-secondary">Adjustment</span>
                            @endif
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="detail-label">Quantity</div>
                        <div class="detail-value fw-bold fs-4
                            {{ $transaction->type === 'in' ? 'text-success' : ($transaction->type === 'out' ? 'text-danger' : 'text-secondary') }}">
                            {{ $transaction->type === 'in' ? '+' : ($transaction->type === 'out' ? '-' : '±') }}{{ $transaction->quantity }}
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="detail-label">Reference Number</div>
                        <div class="detail-value">{{ $transaction->reference ?? '—' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="detail-label">Notes</div>
                        <div class="detail-value">{{ $transaction->notes ?? '—' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="detail-label">Recorded At</div>
                        <div class="detail-value small">
                            {{ $transaction->created_at->format('F d, Y g:i A') }}
                            <span class="text-muted">({{ $transaction->created_at->diffForHumans() }})</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Product Info --}}
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header py-2 px-3">
                    <h6 class="mb-0"><i class="bi bi-box-seam me-1"></i> Product</h6>
                </div>
                <div class="card-body">
                    @if ($transaction->product)
                        <div class="mb-3">
                            <div class="detail-label">Name</div>
                            <div class="detail-value fw-bold">
                                <a href="{{ route('products.show', $transaction->product) }}"
                                   class="text-decoration-none" style="color:var(--dark-green)">
                                    {{ $transaction->product->name }}
                                </a>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="detail-label">SKU</div>
                            <div class="detail-value"><code>{{ $transaction->product->sku }}</code></div>
                        </div>
                        <div class="mb-3">
                            <div class="detail-label">Category</div>
                            <div class="detail-value">{{ $transaction->product->category }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="detail-label">Current Stock</div>
                            <div class="detail-value fw-bold fs-5
                                {{ $transaction->product->isOutOfStock() ? 'text-danger' : ($transaction->product->isLowStock() ? 'text-warning' : 'text-success') }}">
                                {{ $transaction->product->quantity }}
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="detail-label">Stock Status</div>
                            <div class="detail-value">
                                <span class="{{ $transaction->product->stockBadgeClass() }}">
                                    {{ $transaction->product->stockStatus() }}
                                </span>
                            </div>
                        </div>
                        <a href="{{ route('products.show', $transaction->product) }}"
                           class="btn btn-sm btn-outline-secondary w-100">
                            <i class="bi bi-box-seam me-1"></i> View Product Page
                        </a>
                    @else
                        <p class="text-muted small">Product no longer exists.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- User Info --}}
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header py-2 px-3">
                    <h6 class="mb-0"><i class="bi bi-person-fill me-1"></i> Recorded By</h6>
                </div>
                <div class="card-body">
                    @if ($transaction->user)
                        <div class="mb-2">
                            <div class="detail-label">Name</div>
                            <div class="detail-value fw-bold">{{ $transaction->user->name }}</div>
                        </div>
                        <div class="mb-2">
                            <div class="detail-label">Email</div>
                            <div class="detail-value small">{{ $transaction->user->email }}</div>
                        </div>
                    @else
                        <p class="text-muted small">User account no longer exists.</p>
                    @endif
                </div>
            </div>

            {{-- Actions --}}
            <div class="card">
                <div class="card-header py-2 px-3">
                    <h6 class="mb-0"><i class="bi bi-gear me-1"></i> Actions</h6>
                </div>
                <div class="card-body d-flex flex-column gap-2">
                    <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-list-ul me-1"></i> Back to All Transactions
                    </a>
                    <a href="{{ route('transactions.create') }}" class="btn btn-primary-custom btn-sm">
                        <i class="bi bi-plus-circle me-1"></i> Record New Transaction
                    </a>
                    <button type="button" class="btn btn-outline-danger btn-sm"
                            data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="bi bi-trash me-1"></i> Delete & Reverse
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Delete Modal --}}
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
                <p class="mb-2">You are about to delete Transaction <strong>#{{ $transaction->id }}</strong>.</p>
                <div class="alert alert-warning py-2 mb-0 small">
                    <i class="bi bi-info-circle me-1"></i>
                    <strong>Note:</strong> This will reverse the stock change —
                    @if ($transaction->type === 'in')
                        <strong>{{ $transaction->quantity }}</strong> units will be <strong>removed</strong> from
                    @elseif ($transaction->type === 'out')
                        <strong>{{ $transaction->quantity }}</strong> units will be <strong>added back</strong> to
                    @else
                        the quantity adjustment will be reversed for
                    @endif
                    "{{ $transaction->product->name ?? 'this product' }}".
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('transactions.destroy', $transaction) }}">
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
