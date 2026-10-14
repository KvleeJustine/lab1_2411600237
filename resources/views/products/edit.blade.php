@extends('layouts.app')

@section('title', 'Edit Product')

@section('content')
<div class="px-2">
    <div class="d-flex align-items-center mb-3">
        <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline-secondary me-3">
            <i class="bi bi-arrow-left"></i> Back
        </a>
        <h4 class="mb-0 fw-bold" style="color: var(--dark-green);">
            <i class="bi bi-pencil-square me-1"></i> Edit: {{ $product->name }}
        </h4>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header py-2 px-3">
                    <h6 class="mb-0">Edit Product Information</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('products.update', $product) }}">
                        @csrf
                        @method('PUT')
                        @include('products._form')
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary-custom px-4">
                                <i class="bi bi-check-circle me-1"></i> Update Product
                            </button>
                            <a href="{{ route('products.show', $product) }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
