

<?php $__env->startSection('title', 'Record Transaction'); ?>

<?php $__env->startSection('content'); ?>
<div class="px-2">
    <div class="d-flex align-items-center mb-3">
        <a href="<?php echo e(route('transactions.index')); ?>" class="btn btn-sm btn-outline-secondary me-3">
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

                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-circle me-2"></i>
                            <strong>Please fix the following errors:</strong>
                            <ul class="mb-0 mt-1">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?php echo e(route('transactions.store')); ?>">
                        <?php echo csrf_field(); ?>

                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Product <span class="text-danger">*</span>
                            </label>
                            <select name="product_id" id="productSelect"
                                    class="form-select <?php $__errorArgs = ['product_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    required>
                                <option value="">Select a product...</option>
                                <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($p->id); ?>"
                                            data-qty="<?php echo e($p->quantity); ?>"
                                            data-name="<?php echo e($p->name); ?>"
                                            data-status="<?php echo e($p->stockStatus()); ?>"
                                            <?php echo e(old('product_id') == $p->id ? 'selected' : ''); ?>>
                                        <?php echo e($p->name); ?> (<?php echo e($p->sku); ?>) — Qty: <?php echo e($p->quantity); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['product_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                            
                            <div id="stockInfo" class="mt-2 d-none">
                                <div class="alert py-2 mb-0 small" id="stockInfoAlert">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Current stock: <strong id="stockQty"></strong>
                                    &mdash; <span id="stockStatusLabel"></span>
                                </div>
                            </div>
                        </div>

                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Transaction Type <span class="text-danger">*</span>
                            </label>
                            <div class="row g-2">
                                <?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-4">
                                    <input type="radio" class="btn-check" name="type"
                                           id="type_<?php echo e($t); ?>" value="<?php echo e($t); ?>"
                                           <?php echo e(old('type', 'in') === $t ? 'checked' : ''); ?>>
                                    <label class="btn btn-outline-<?php echo e($t === 'in' ? 'success' : ($t === 'out' ? 'danger' : 'secondary')); ?> w-100"
                                           for="type_<?php echo e($t); ?>">
                                        <?php if($t === 'in'): ?>
                                            <i class="bi bi-box-arrow-in-down"></i> Stock In
                                        <?php elseif($t === 'out'): ?>
                                            <i class="bi bi-box-arrow-up"></i> Stock Out
                                        <?php else: ?>
                                            <i class="bi bi-sliders"></i> Adjustment
                                        <?php endif; ?>
                                    </label>
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            <?php $__errorArgs = ['type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="text-danger small mt-1"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Quantity <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="quantity" id="quantityInput"
                                   class="form-control <?php $__errorArgs = ['quantity'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   value="<?php echo e(old('quantity', 1)); ?>"
                                   min="1" required>
                            <div class="form-text" id="qtyHint">Enter the quantity for this transaction.</div>
                            <?php $__errorArgs = ['quantity'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Reference Number</label>
                            <input type="text" name="reference"
                                   class="form-control <?php $__errorArgs = ['reference'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   value="<?php echo e(old('reference')); ?>"
                                   placeholder="e.g. PO-2026-001, SO-0042, ADJ-001"
                                   maxlength="100">
                            <div class="form-text">Purchase order, sales order, or adjustment reference.</div>
                            <?php $__errorArgs = ['reference'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Notes</label>
                            <textarea name="notes"
                                      class="form-control <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                      rows="3"
                                      placeholder="Optional: additional context for this transaction..."
                                      maxlength="1000"><?php echo e(old('notes')); ?></textarea>
                            <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary-custom px-4">
                                <i class="bi bi-check-circle me-1"></i> Save Transaction
                            </button>
                            <a href="<?php echo e(route('transactions.index')); ?>" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        
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
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Lab6_2411600237\inventory\resources\views/transactions/create.blade.php ENDPATH**/ ?>