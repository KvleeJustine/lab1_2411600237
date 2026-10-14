

<?php $__env->startSection('title', 'Inventory'); ?>

<?php $__env->startSection('content'); ?>
<div class="px-2">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0 fw-bold" style="color: var(--dark-green);">
                <i class="bi bi-box-seam me-1"></i> Inventory Products
            </h4>
            <small class="text-muted"><?php echo e($products->total()); ?> product(s) total</small>
        </div>
        <a href="<?php echo e(route('products.create')); ?>" class="btn btn-primary-custom">
            <i class="bi bi-plus-circle"></i> Add Product
        </a>
    </div>

    
    <div class="card mb-3">
        <div class="card-body py-2">
            <form method="GET" action="<?php echo e(route('products.index')); ?>" class="row g-2 align-items-end">
                <div class="col-12 col-md-4">
                    <label class="form-label small fw-semibold mb-1">Search</label>
                    <input type="text" name="search" class="form-control form-control-sm"
                           placeholder="Product name or SKU..."
                           value="<?php echo e(request('search')); ?>">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small fw-semibold mb-1">Category</label>
                    <select name="category" class="form-select form-select-sm">
                        <option value="all">All Categories</option>
                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($cat); ?>" <?php echo e(request('category') == $cat ? 'selected' : ''); ?>>
                                <?php echo e($cat); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small fw-semibold mb-1">Stock Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Status</option>
                        <option value="in"  <?php echo e(request('status') == 'in'  ? 'selected' : ''); ?>>In Stock</option>
                        <option value="low" <?php echo e(request('status') == 'low' ? 'selected' : ''); ?>>Low Stock</option>
                        <option value="out" <?php echo e(request('status') == 'out' ? 'selected' : ''); ?>>Out of Stock</option>
                    </select>
                </div>
                <div class="col-12 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary-custom btn-sm flex-fill">
                        <i class="bi bi-search"></i> Filter
                    </button>
                    <a href="<?php echo e(route('products.index')); ?>" class="btn btn-outline-secondary btn-sm flex-fill">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr class="table-header">
                            <th>Product Name</th>
                            <th>SKU</th>
                            <th>Category</th>
                            <th>Qty</th>
                            <th>Reorder Lvl</th>
                            <th>Unit Price</th>
                            <th>Supplier</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="<?php echo e($product->isOutOfStock() ? 'out-of-stock-row' : ($product->isLowStock() ? 'low-stock-row' : '')); ?>">
                            <td>
                                <a href="<?php echo e(route('products.show', $product)); ?>"
                                   class="fw-semibold text-decoration-none"
                                   style="color: var(--dark-green);"><?php echo e($product->name); ?></a>
                            </td>
                            <td><code class="small"><?php echo e($product->sku); ?></code></td>
                            <td><?php echo e($product->category); ?></td>
                            <td>
                                <span class="fw-bold <?php echo e($product->isOutOfStock() ? 'text-danger' : ($product->isLowStock() ? 'text-warning' : '')); ?>">
                                    <?php echo e($product->quantity); ?>

                                </span>
                            </td>
                            <td><?php echo e($product->reorder_level); ?></td>
                            <td>₱<?php echo e(number_format($product->unit_price, 2)); ?></td>
                            <td class="small text-muted"><?php echo e($product->supplier ?? '—'); ?></td>
                            <td>
                                <span class="<?php echo e($product->stockBadgeClass()); ?>"><?php echo e($product->stockStatus()); ?></span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="<?php echo e(route('products.show', $product)); ?>"
                                       class="btn btn-outline-secondary" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="<?php echo e(route('products.edit', $product)); ?>"
                                       class="btn btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger"
                                            title="Delete"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteModal"
                                            data-product-id="<?php echo e($product->id); ?>"
                                            data-product-name="<?php echo e($product->name); ?>">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="bi bi-box-seam" style="font-size:2rem;"></i>
                                <p class="mt-2 mb-0">No products found.
                                    <a href="<?php echo e(route('products.create')); ?>">Add your first product!</a>
                                </p>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php if($products->hasPages()): ?>
        <div class="card-footer bg-white d-flex justify-content-between align-items-center">
            <small class="text-muted">
                Showing <?php echo e($products->firstItem()); ?>–<?php echo e($products->lastItem()); ?> of <?php echo e($products->total()); ?>

            </small>
            <?php echo e($products->links()); ?>

        </div>
        <?php endif; ?>
    </div>
</div>


<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header modal-header-custom">
                <h5 class="modal-title"><i class="bi bi-exclamation-triangle me-2"></i>Delete Product?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-1">You are about to delete:</p>
                <p class="fw-bold" id="deleteProductName" style="color: var(--dark-green);"></p>
                <p class="text-danger small mb-0">
                    <i class="bi bi-exclamation-circle"></i> This action cannot be undone.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i> Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
document.getElementById('deleteModal').addEventListener('show.bs.modal', function (e) {
    const btn  = e.relatedTarget;
    const id   = btn.getAttribute('data-product-id');
    const name = btn.getAttribute('data-product-name');
    document.getElementById('deleteProductName').textContent = name;
    document.getElementById('deleteForm').action = '/products/' + id;
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Lab6_2411600237\inventory\resources\views/products/index.blade.php ENDPATH**/ ?>