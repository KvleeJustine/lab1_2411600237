@extends('layouts.app')

@section('title', 'Record Transaction')

@section('content')
<div class="px-2">
    <div class="d-flex align-items-center mb-3">
        <a href="{{ route('transactions.index') }}" class="btn btn-sm btn-outline-secondary me-3">
            <i class="bi bi-arrow-left"></i> Back
        </a>
        <h4 class="mb-0 fw-bold" style="color: var(--dark-green);">
            <i class="bi bi-plus-circle me-1"></i> Record Inventory Transaction
        </h4>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card">
                <div class="card-header py-2 px-3">
                    <h6 class="mb-0">Transaction Details</h6>
                </div>
                <div class="card-body">

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-circle me-2"></i>
                            <strong>Please fix the following errors:</strong>
                            <ul class="mb-0 mt-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('transactions.store') }}">
                        @csrf

                        {{-- Product Selection --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Product <span class="text-danger">*</span>
                            </label>
                            <select name="product_id" id="productSelect"
                                    class="form-select @error('product_id') is-invalid @enderror"
                                    required>
                                <option value="">Select a product...</option>
                                @foreach ($products as $p)
                                    <option value="{{ $p->id }}"
                                            data-qty="{{ $p->quantity }}"
                                            data-name="{{ $p->name }}"
                                            data-status="{{ $p->stockStatus() }}"
                                            {{ old('product_id') == $p->id ? 'selected' : '' }}>
                                        {{ $p->name }} ({{ $p->sku }}) — Qty: {{ $p->quantity }}
                                    </option>
                                @endforeach
                            </select>
                            @error('product_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            {{-- Current stock indicator (shown via JS) --}}
                            <div id="stockInfo" class="mt-2 d-none">
                                <div class="alert py-2 mb-0 small" id="stockInfoAlert">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Current stock: <strong id="stockQty"></strong>
                                    &mdash; <span id="stockStatusLabel"></span>
                                </div>
                            </div>
                        </div>

                        {{-- Transaction Type --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Transaction Type <span class="text-danger">*</span>
                            </label>
                            <div class="row g-2">
                                @foreach ($types as $t)
                                <div class="col-4">
                                    <input type="radio" class="btn-check" name="type"
                                           id="type_{{ $t }}" value="{{ $t }}"
                                           {{ old('type', 'in') === $t ? 'checked' : '' }}>
                                    <label class="btn btn-outline-{{ $t === 'in' ? 'success' : ($t === 'out' ? 'danger' : 'secondary') }} w-100"
                                           for="type_{{ $t }}">
                                        @if ($t === 'in')
                                            <i class="bi bi-box-arrow-in-down"></i> Stock In
                                        @elseif ($t === 'out')
                                            <i class="bi bi-box-arrow-up"></i> Stock Out
                                        @else
                                            <i class="bi bi-sliders"></i> Adjustment
                                        @endif
                                    </label>
                                </div>
                                @endforeach
                            </div>
                            @error('type')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Quantity --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Quantity <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="quantity" id="quantityInput"
                                   class="form-control @error('quantity') is-invalid @enderror"
                                   value="{{ old('quantity', 1) }}"
                                   min="1" required>
                            <div class="form-text" id="qtyHint">Enter the quantity for this transaction.</div>
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Reference Number --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Reference Number</label>
                            <input type="text" name="reference"
                                   class="form-control @error('reference') is-invalid @enderror"
                                   value="{{ old('reference') }}"
                                   placeholder="e.g. PO-2026-001, SO-0042, ADJ-001"
                                   maxlength="100">
                            <div class="form-text">Purchase order, sales order, or adjustment reference.</div>
                            @error('reference')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Notes --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Notes</label>
                            <textarea name="notes"
                                      class="form-control @error('notes') is-invalid @enderror"
                                      rows="3"
                                      placeholder="Optional: additional context for this transaction..."
                                      maxlength="1000">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary-custom px-4">
                                <i class="bi bi-check-circle me-1"></i> Save Transaction
                            </button>
                            <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Quick info sidebar --}}
        <div class="col-md-5">
            <div class="card">
                <div class="card-header py-2 px-3">
                    <h6 class="mb-0"><i class="bi bi-info-circle me-1"></i> Transaction Guide</h6>
                </div>
                <div class="card-body small">
                    <div class="mb-3">
                        <span class="badge bg-success mb-1">Stock In</span>
                        <p class="mb-0 text-muted">
                            Use when receiving goods from a supplier, processing a purchase order,
                            or returning items to inventory.
                        </p>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <span class="badge bg-danger mb-1">Stock Out</span>
                        <p class="mb-0 text-muted">
                            Use when fulfilling a sales order, issuing items to a project,
                            or writing off damaged goods. Quantity cannot exceed current stock.
                        </p>
                    </div>
                    <hr>
                    <div>
                        <span class="badge bg-secondary mb-1">Adjustment</span>
                        <p class="mb-0 text-muted">
                            Use to correct discrepancies found during a physical count or to record
                            minor corrections. Always add a reference and notes explaining the reason.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    const productSelect = document.getElementById('productSelect');
    const stockInfo     = document.getElementById('stockInfo');
    const stockInfoAlert= document.getElementById('stockInfoAlert');
    const stockQty      = document.getElementById('stockQty');
    const stockStatus   = document.getElementById('stockStatusLabel');
    const qtyInput      = document.getElementById('quantityInput');
    const qtyHint       = document.getElementById('qtyHint');

    function updateStockInfo() {
        const selected = productSelect.options[productSelect.selectedIndex];
        if (!selected || !selected.value) {
            stockInfo.classList.add('d-none');
            return;
        }
        const qty    = parseInt(selected.dataset.qty);
        const status = selected.dataset.status;

        stockQty.textContent    = qty;
        stockStatus.textContent = status;

        // Set alert colour based on status
        stockInfoAlert.className = 'alert py-2 mb-0 small alert-' +
            (status === 'Out of Stock' ? 'danger' : (status === 'Low Stock' ? 'warning' : 'info'));

        stockInfo.classList.remove('d-none');

        // Update quantity hint based on selected type
        updateQtyHint(qty);
    }

    function updateQtyHint(qty) {
        const typeEl = document.querySelector('input[name="type"]:checked');
        if (!typeEl) return;
        if (typeEl.value === 'out') {
            qtyHint.textContent = `Max available to issue: ${qty}`;
            qtyInput.max = qty;
        } else {
            qtyHint.textContent = 'Enter the quantity for this transaction.';
            qtyInput.removeAttribute('max');
        }
    }

    productSelect.addEventListener('change', updateStockInfo);

    document.querySelectorAll('input[name="type"]').forEach(radio => {
        radio.addEventListener('change', function () {
            const selected = productSelect.options[productSelect.selectedIndex];
            if (selected && selected.value) {
                updateQtyHint(parseInt(selected.dataset.qty));
            }
        });
    });

    // Trigger on load if a product is already selected (e.g., after validation error)
    if (productSelect.value) updateStockInfo();
})();
</script>
@endsection
