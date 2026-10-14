{{-- Shared form partial for create and edit --}}
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

<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label fw-semibold">Product Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $product->name ?? '') }}"
               placeholder="e.g. 2x4 Lumber (8ft)" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">SKU <span class="text-danger">*</span></label>
        <input type="text" name="sku" class="form-control @error('sku') is-invalid @enderror"
               value="{{ old('sku', $product->sku ?? '') }}"
               placeholder="e.g. LUM-001" required>
        @error('sku')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
        <select name="category" class="form-select @error('category') is-invalid @enderror" required>
            <option value="">Select category...</option>
            @foreach (['Lumber','Hardware','Tools','Electrical','Plumbing','Paint','Other'] as $cat)
                <option value="{{ $cat }}"
                    {{ old('category', $product->category ?? '') == $cat ? 'selected' : '' }}>
                    {{ $cat }}
                </option>
            @endforeach
        </select>
        @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold">Supplier</label>
        <input type="text" name="supplier" class="form-control @error('supplier') is-invalid @enderror"
               value="{{ old('supplier', $product->supplier ?? '') }}"
               placeholder="e.g. PhilLumber Corp">
        @error('supplier')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Quantity <span class="text-danger">*</span></label>
        <input type="number" name="quantity" class="form-control @error('quantity') is-invalid @enderror"
               value="{{ old('quantity', $product->quantity ?? 0) }}"
               min="0" required>
        @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Reorder Level <span class="text-danger">*</span></label>
        <input type="number" name="reorder_level" class="form-control @error('reorder_level') is-invalid @enderror"
               value="{{ old('reorder_level', $product->reorder_level ?? 10) }}"
               min="0" required>
        <div class="form-text">Alert when stock falls to this level.</div>
        @error('reorder_level')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Unit Price (PHP) <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text">₱</span>
            <input type="number" name="unit_price" step="0.01"
                   class="form-control @error('unit_price') is-invalid @enderror"
                   value="{{ old('unit_price', $product->unit_price ?? '') }}"
                   min="0.01" placeholder="0.00" required>
            @error('unit_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-12">
        <label class="form-label fw-semibold">Description</label>
        <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                  rows="3" placeholder="Optional: describe the product...">{{ old('description', $product->description ?? '') }}</textarea>
        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>
