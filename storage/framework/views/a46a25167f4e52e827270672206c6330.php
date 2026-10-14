

<?php $__env->startSection('title', 'Inventory Reports'); ?>

<?php $__env->startSection('content'); ?>
<div class="px-2">

    
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0 fw-bold" style="color: var(--dark-green);">
                <i class="bi bi-file-earmark-bar-graph me-1"></i> Inventory Reports
            </h4>
            <small class="text-muted">Generated on <?php echo e(now()->format('F d, Y g:i A')); ?></small>
        </div>
        <div class="d-flex gap-2">
            <a href="<?php echo e(route('dashboard')); ?>" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-speedometer2 me-1"></i> Dashboard
            </a>
            <a href="<?php echo e(route('transactions.index')); ?>" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left-right me-1"></i> Transactions
            </a>
        </div>
    </div>

    
    
    
    <div class="card mb-4">
        <div class="card-header py-2 px-3">
            <h6 class="mb-0">
                <i class="bi bi-arrow-left-right me-1"></i> Stock Movement Report
            </h6>
        </div>
        <div class="card-body border-bottom pb-3">
            
            <form method="GET" action="<?php echo e(route('reports.index')); ?>" class="row g-2 align-items-end">
                <div class="col-6 col-md-3">
                    <label class="form-label small fw-semibold mb-1">Date From</label>
                    <input type="date" name="date_from" class="form-control form-control-sm"
                           value="<?php echo e(request('date_from')); ?>">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small fw-semibold mb-1">Date To</label>
                    <input type="date" name="date_to" class="form-control form-control-sm"
                           value="<?php echo e(request('date_to')); ?>">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small fw-semibold mb-1">Type</label>
                    <select name="movement_type" class="form-select form-select-sm">
                        <option value="all">All Types</option>
                        <option value="in"         <?php echo e(request('movement_type') === 'in'         ? 'selected' : ''); ?>>Stock In</option>
                        <option value="out"        <?php echo e(request('movement_type') === 'out'        ? 'selected' : ''); ?>>Stock Out</option>
                        <option value="adjustment" <?php echo e(request('movement_type') === 'adjustment' ? 'selected' : ''); ?>>Adjustment</option>
                    </select>
                </div>
                <div class="col-6 col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary-custom btn-sm flex-fill">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                    <a href="<?php echo e(route('reports.index')); ?>" class="btn btn-outline-secondary btn-sm flex-fill">
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
                        <?php $__empty_1 = true; $__currentLoopData = $movements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tx): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="small text-muted">
                                <?php echo e($tx->created_at->format('M d, Y g:i A')); ?>

                            </td>
                            <td>
                                <a href="<?php echo e(route('products.show', $tx->product_id)); ?>"
                                   class="text-decoration-none fw-semibold small"
                                   style="color:var(--dark-green)">
                                    <?php echo e($tx->product->name ?? '—'); ?>

                                </a>
                            </td>
                            <td><code class="small"><?php echo e($tx->product->sku ?? '—'); ?></code></td>
                            <td>
                                <?php if($tx->type === 'in'): ?>
                                    <span class="badge bg-success">Stock In</span>
                                <?php elseif($tx->type === 'out'): ?>
                                    <span class="badge bg-danger">Stock Out</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Adjustment</span>
                                <?php endif; ?>
                            </td>
                            <td class="fw-bold <?php echo e($tx->type === 'in' ? 'text-success' : ($tx->type === 'out' ? 'text-danger' : 'text-secondary')); ?>">
                                <?php echo e($tx->type === 'in' ? '+' : ($tx->type === 'out' ? '-' : '±')); ?><?php echo e($tx->quantity); ?>

                            </td>
                            <td class="small"><?php echo e($tx->reference ?? '—'); ?></td>
                            <td class="small text-muted"><?php echo e($tx->user->name ?? 'System'); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox" style="font-size:2rem;"></i>
                                <p class="mt-2 mb-0">No transactions found for the selected filters.</p>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if($movements->hasPages()): ?>
            <div class="px-3 py-2 border-top d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Showing <?php echo e($movements->firstItem()); ?>–<?php echo e($movements->lastItem()); ?>

                    of <?php echo e($movements->total()); ?> transactions
                </small>
                <?php echo e($movements->links()); ?>

            </div>
            <?php endif; ?>
        </div>
    </div>

    
    
    
    <div class="card mb-4">
        <div class="card-header py-2 px-3 d-flex justify-content-between align-items-center">
            <h6 class="mb-0">
                <i class="bi bi-exclamation-triangle-fill me-1 text-warning"></i> Low Stock Report
            </h6>
            <span class="badge rounded-pill bg-warning text-dark">
                <?php echo e($lowStockProducts->count()); ?> item(s)
            </span>
        </div>
        <div class="card-body p-0">
            <?php if($lowStockProducts->isEmpty()): ?>
                <p class="text-center text-muted py-4 mb-0">
                    <i class="bi bi-check-circle text-success me-1"></i>
                    All products are sufficiently stocked.
                </p>
            <?php else: ?>
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
                            <?php $__currentLoopData = $lowStockProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="<?php echo e($p->isOutOfStock() ? 'out-of-stock-row' : 'low-stock-row'); ?>">
                                <td>
                                    <a href="<?php echo e(route('products.show', $p)); ?>"
                                       class="fw-semibold text-decoration-none"
                                       style="color:var(--dark-green)"><?php echo e($p->name); ?></a>
                                </td>
                                <td><code class="small"><?php echo e($p->sku); ?></code></td>
                                <td><?php echo e($p->category); ?></td>
                                <td class="fw-bold <?php echo e($p->isOutOfStock() ? 'text-danger' : 'text-warning'); ?>">
                                    <?php echo e($p->quantity); ?>

                                </td>
                                <td><?php echo e($p->reorder_level); ?></td>
                                <td class="text-danger fw-bold">
                                    <?php echo e(max(0, $p->reorder_level - $p->quantity)); ?> units short
                                </td>
                                <td>
                                    <span class="<?php echo e($p->stockBadgeClass()); ?>"><?php echo e($p->stockStatus()); ?></span>
                                </td>
                                <td class="small text-muted"><?php echo e($p->supplier ?? '—'); ?></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    
    
    
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
                        <?php $__currentLoopData = $valuationByCategory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="fw-semibold"><?php echo e($row->category); ?></td>
                            <td class="text-center"><?php echo e($row->product_count); ?></td>
                            <td class="text-center"><?php echo e(number_format($row->total_units)); ?></td>
                            <td class="text-end fw-bold" style="color:var(--primary);">
                                ₱<?php echo e(number_format($row->total_value, 2)); ?>

                            </td>
                            <td class="text-end">
                                <?php
                                    $pct = $grandTotalValue > 0
                                        ? round(($row->total_value / $grandTotalValue) * 100, 1)
                                        : 0;
                                ?>
                                <div class="d-flex align-items-center justify-content-end gap-2">
                                    <div class="progress flex-grow-1" style="height:8px; max-width:80px;">
                                        <div class="progress-bar"
                                             style="width:<?php echo e($pct); ?>%; background-color:var(--primary);"></div>
                                    </div>
                                    <span class="small"><?php echo e($pct); ?>%</span>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                    <tfoot>
                        <tr style="background-color: var(--light-accent); font-weight:700;">
                            <td>TOTAL</td>
                            <td class="text-center">—</td>
                            <td class="text-center"><?php echo e(number_format($grandTotalUnits)); ?></td>
                            <td class="text-end" style="color:var(--dark-green);">
                                ₱<?php echo e(number_format($grandTotalValue, 2)); ?>

                            </td>
                            <td class="text-end">100%</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Lab6_2411600237\inventory\resources\views/reports/index.blade.php ENDPATH**/ ?>