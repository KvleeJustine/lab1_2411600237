

<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="px-2">

    
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0 fw-bold" style="color: var(--dark-green);">🏠 Dashboard</h4>
            <small class="text-muted">
                Hardware Inventory Overview &mdash; <?php echo e(now()->format('F d, Y')); ?>

                &bull; Last updated: <span id="lastUpdated">just now</span>
            </small>
        </div>
        <div class="d-flex gap-2">
            <a href="<?php echo e(route('transactions.create')); ?>" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left-right"></i> Record Transaction
            </a>
            <a href="<?php echo e(route('products.create')); ?>" class="btn btn-primary-custom btn-sm">
                <i class="bi bi-plus-circle"></i> Add Product
            </a>
        </div>
    </div>

    
    <?php if($lowStockItems > 0 || $outOfStockItems > 0): ?>
    <div class="alert alert-warning alert-dismissible fade show mb-3" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <strong>Stock Alert:</strong>
        <?php if($outOfStockItems > 0): ?>
            <span class="text-danger fw-bold"><?php echo e($outOfStockItems); ?> item(s) out of stock</span>
        <?php endif; ?>
        <?php if($lowStockItems > 0 && $outOfStockItems > 0): ?> &nbsp;&bull;&nbsp; <?php endif; ?>
        <?php if($lowStockItems > 0): ?>
            <span style="color:#78350f;"><?php echo e($lowStockItems); ?> item(s) running low</span>
        <?php endif; ?>
        &mdash; <a href="<?php echo e(route('products.index', ['status' => 'low'])); ?>" class="fw-bold"
                   style="color:var(--dark-green);">View items</a>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card stat-card p-3 text-center">
                <div class="stat-icon">📦</div>
                <div class="stat-label">Total Products</div>
                <div class="stat-value" id="statTotal"><?php echo e($totalProducts); ?></div>
                <div class="stat-sub">items in inventory</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card p-3 text-center">
                <div class="stat-icon">⚠️</div>
                <div class="stat-label">Low Stock</div>
                <div class="stat-value" id="statLow" style="color: var(--accent);"><?php echo e($lowStockItems); ?></div>
                <div class="stat-sub">need reordering</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card p-3 text-center">
                <div class="stat-icon">🚫</div>
                <div class="stat-label">Out of Stock</div>
                <div class="stat-value" id="statOut" style="color: var(--accent-red);"><?php echo e($outOfStockItems); ?></div>
                <div class="stat-sub">zero quantity</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card p-3 text-center">
                <div class="stat-icon">💰</div>
                <div class="stat-label">Inventory Value</div>
                <div class="stat-value" id="statValue" style="font-size:1.4rem;">
                    ₱<?php echo e(number_format($inventoryValue, 2)); ?>

                </div>
                <div class="stat-sub">total stock value</div>
            </div>
        </div>
    </div>

    
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header py-2 px-3">
                    <h6 class="mb-0"><i class="bi bi-pie-chart-fill me-1"></i> Products by Category</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container"><canvas id="categoryChart"></canvas></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header py-2 px-3">
                    <h6 class="mb-0"><i class="bi bi-clipboard-data me-1"></i> Stock Status Distribution</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container"><canvas id="statusChart"></canvas></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header py-2 px-3">
                    <h6 class="mb-0"><i class="bi bi-bar-chart-fill me-1"></i> Top 5 by Inventory Value</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container"><canvas id="topValueChart"></canvas></div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="row g-3">

        
        <div class="col-md-8">
            <div class="card">
                <div class="card-header py-2 px-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-arrow-left-right me-1"></i> Recent Transactions</h6>
                    <a href="<?php echo e(route('transactions.index')); ?>" class="btn btn-sm btn-primary-custom">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="recentTxTable">
                            <thead>
                                <tr class="table-header">
                                    <th>Date</th>
                                    <th>Product</th>
                                    <th>Type</th>
                                    <th>Qty</th>
                                    <th>By</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $recentTransactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tx): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td class="small text-muted"><?php echo e($tx->created_at->format('M d, g:i A')); ?></td>
                                    <td>
                                        <a href="<?php echo e(route('products.show', $tx->product_id)); ?>"
                                           class="text-decoration-none fw-semibold small"
                                           style="color:var(--dark-green)">
                                            <?php echo e($tx->product->name ?? '—'); ?>

                                        </a>
                                    </td>
                                    <td>
                                        <?php if($tx->type === 'in'): ?>
                                            <span class="badge bg-success">IN</span>
                                        <?php elseif($tx->type === 'out'): ?>
                                            <span class="badge bg-danger">OUT</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">ADJ</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="fw-bold <?php echo e($tx->type === 'in' ? 'text-success' : ($tx->type === 'out' ? 'text-danger' : '')); ?>">
                                        <?php echo e($tx->type === 'in' ? '+' : ($tx->type === 'out' ? '-' : '±')); ?><?php echo e($tx->quantity); ?>

                                    </td>
                                    <td class="small text-muted"><?php echo e($tx->user->name ?? 'System'); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-3 text-muted small">
                                        No transactions yet.
                                        <a href="<?php echo e(route('transactions.create')); ?>">Record one!</a>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="col-md-4">

            
            <div class="card mb-3">
                <div class="card-header py-2 px-3">
                    <h6 class="mb-0"><i class="bi bi-exclamation-diamond-fill me-1 text-warning"></i> Low Stock Alerts</h6>
                </div>
                <div class="card-body p-0" id="lowStockList">
                    <?php $__empty_1 = true; $__currentLoopData = $recentProducts->filter(fn($p) => $p->isLowStock() || $p->isOutOfStock())->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                        <div>
                            <a href="<?php echo e(route('products.show', $p)); ?>"
                               class="fw-semibold text-decoration-none small" style="color:var(--dark-green)">
                                <?php echo e($p->name); ?>

                            </a>
                            <br>
                            <small class="text-muted">Qty: <?php echo e($p->quantity); ?> / Reorder: <?php echo e($p->reorder_level); ?></small>
                        </div>
                        <span class="<?php echo e($p->stockBadgeClass()); ?>"><?php echo e($p->stockStatus()); ?></span>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-center text-muted small py-3 mb-0">
                        <i class="bi bi-check-circle me-1 text-success"></i> All items are well-stocked.
                    </p>
                    <?php endif; ?>
                </div>
            </div>

            
            <div class="card">
                <div class="card-header py-2 px-3">
                    <h6 class="mb-0"><i class="bi bi-tags-fill me-1"></i> Category Summary</h6>
                </div>
                <div class="card-body">
                    <?php $__empty_1 = true; $__currentLoopData = $categoryData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small fw-semibold"><?php echo e($cat->category); ?></span>
                        <span class="badge rounded-pill" style="background-color:var(--primary);">
                            <?php echo e($cat->count); ?>

                        </span>
                    </div>
                    <div class="progress mb-3" style="height:6px;">
                        <div class="progress-bar"
                             style="width: <?php echo e($totalProducts > 0 ? round(($cat->count / $totalProducts) * 100) : 0); ?>%;
                                    background-color:var(--primary);"></div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-muted small">No data yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
// ── Chart Colors ─────────────────────────────────────────────────────────────
const CHART_COLORS = {
    green:  ["#14532D","#1a936f","#198754","#2db37a","#6dcca4","#d4ece4"],
    status: { "In Stock": "#1a936f", "Low Stock": "#f3a712", "Out of Stock": "#dc3545" },
    border: "rgba(255,255,255,0.8)"
};

Chart.defaults.font.family = "'Segoe UI', Arial, sans-serif";
Chart.defaults.font.size   = 12;
Chart.defaults.color       = "#4B5563";

// ── Category Doughnut ─────────────────────────────────────────────────────────
const catLabels = <?php echo json_encode($categoryData->pluck('category'), 15, 512) ?>;
const catValues = <?php echo json_encode($categoryData->pluck('count'), 15, 512) ?>;

new Chart(document.getElementById('categoryChart'), {
    type: 'doughnut',
    data: {
        labels: catLabels,
        datasets: [{
            data: catValues,
            backgroundColor: CHART_COLORS.green,
            borderColor: CHART_COLORS.border,
            borderWidth: 2
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, padding: 8, font: { size: 11 } } } }
    }
});

// ── Status Pie ────────────────────────────────────────────────────────────────
const statusData = <?php echo json_encode($statusData, 15, 512) ?>;

new Chart(document.getElementById('statusChart'), {
    type: 'pie',
    data: {
        labels: Object.keys(statusData),
        datasets: [{
            data: Object.values(statusData),
            backgroundColor: Object.keys(statusData).map(k => CHART_COLORS.status[k]),
            borderColor: CHART_COLORS.border,
            borderWidth: 2
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, padding: 8, font: { size: 11 } } } }
    }
});

// ── Top Items Bar ─────────────────────────────────────────────────────────────
const topItems  = <?php echo json_encode($topValueItems, 15, 512) ?>;
const topLabels = topItems.map(p => p.name.length > 18 ? p.name.substring(0,18)+'...' : p.name);
const topValues = topItems.map(p => parseFloat(p.total_value));

new Chart(document.getElementById('topValueChart'), {
    type: 'bar',
    data: {
        labels: topLabels,
        datasets: [{
            label: 'Inventory Value (PHP)',
            data: topValues,
            backgroundColor: CHART_COLORS.green,
            borderRadius: 6
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true, maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => ' PHP ' + ctx.raw.toLocaleString('en-PH', { minimumFractionDigits: 2 })
                }
            }
        },
        scales: {
            x: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
            y: { grid: { display: false }, ticks: { font: { size: 10 } } }
        }
    }
});

// ── AJAX Polling — refresh stat cards every 30 seconds ───────────────────────
(function () {
    const BASE = window.location.origin + '/api';

    function animateValue(el, newVal) {
        if (!el) return;
        el.style.transition = 'opacity 0.3s';
        el.style.opacity    = '0';
        setTimeout(() => {
            el.textContent  = newVal;
            el.style.opacity = '1';
        }, 300);
    }

    function formatPHP(value) {
        return '₱' + parseFloat(value).toLocaleString('en-PH', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    async function refreshStats() {
        try {
            const res  = await fetch(BASE + '/dashboard/stats', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!res.ok) return;
            const data = await res.json();

            animateValue(document.getElementById('statTotal'), data.total_products);
            animateValue(document.getElementById('statLow'),   data.low_stock_items);
            animateValue(document.getElementById('statOut'),   data.out_of_stock);
            animateValue(document.getElementById('statValue'), formatPHP(data.inventory_value));

            const ts = new Date(data.updated_at);
            document.getElementById('lastUpdated').textContent =
                ts.toLocaleTimeString('en-PH', { hour: '2-digit', minute: '2-digit', second: '2-digit' });

        } catch (err) {
            console.warn('Dashboard stats refresh failed:', err);
        }
    }

    // Poll every 30 seconds
    setInterval(refreshStats, 30000);
})();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Lab6_2411600237\inventory\resources\views/dashboard.blade.php ENDPATH**/ ?>