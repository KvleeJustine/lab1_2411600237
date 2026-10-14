<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Hardware Inventory'); ?> — KyleHardware</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?php echo e(asset('css/app.css')); ?>" rel="stylesheet">
    <?php echo $__env->yieldContent('head'); ?>
</head>
<body>

<!-- ===== NAVBAR ===== -->
<nav class="navbar navbar-expand-lg dashboard-navbar sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="<?php echo e(route('dashboard')); ?>">🔨 KyleHardware</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>"
                       href="<?php echo e(route('dashboard')); ?>">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('products.*') ? 'active' : ''); ?>"
                       href="<?php echo e(route('products.index')); ?>">
                        <i class="bi bi-box-seam"></i> Inventory
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('transactions.*') ? 'active' : ''); ?>"
                       href="<?php echo e(route('transactions.index')); ?>">
                        <i class="bi bi-arrow-left-right"></i> Transactions
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('reports.*') ? 'active' : ''); ?>"
                       href="<?php echo e(route('reports.index')); ?>">
                        <i class="bi bi-file-earmark-bar-graph"></i> Reports
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('products.create') ? 'active' : ''); ?>"
                       href="<?php echo e(route('products.create')); ?>">
                        <i class="bi bi-plus-circle"></i> Add Product
                    </a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-3">

                
                <?php if(auth()->guard()->check()): ?>
                <?php
                    $notifications    = Auth::user()->unreadNotifications()->latest()->take(5)->get();
                    $notifCount       = Auth::user()->unreadNotifications()->count();
                ?>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-light position-relative"
                            type="button" data-bs-toggle="dropdown" aria-expanded="false"
                            title="Notifications">
                        <i class="bi bi-bell-fill"></i>
                        <?php if($notifCount > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                  style="font-size:0.6rem;">
                                <?php echo e($notifCount > 9 ? '9+' : $notifCount); ?>

                            </span>
                        <?php endif; ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow" style="min-width:320px; max-width:360px;">
                        <li class="dropdown-header d-flex justify-content-between align-items-center px-3 py-2">
                            <span class="fw-bold small" style="color:var(--dark-green);">
                                <i class="bi bi-bell me-1"></i> Notifications
                            </span>
                            <?php if($notifCount > 0): ?>
                            <form method="POST" action="<?php echo e(route('notifications.readAll')); ?>">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn btn-link btn-sm p-0 text-muted" style="font-size:0.75rem;">
                                    Mark all read
                                </button>
                            </form>
                            <?php endif; ?>
                        </li>
                        <li><hr class="dropdown-divider my-0"></li>

                        <?php $__empty_1 = true; $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notif): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php $data = $notif->data; ?>
                        <li>
                            <div class="dropdown-item py-2 px-3" style="white-space:normal; cursor:default;">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1 me-2">
                                        <span class="badge bg-<?php echo e($data['status'] === 'Out of Stock' ? 'danger' : 'warning'); ?> mb-1"
                                              style="font-size:0.65rem;">
                                            <?php echo e($data['status']); ?>

                                        </span>
                                        <p class="mb-0 small fw-semibold"><?php echo e($data['product_name']); ?></p>
                                        <p class="mb-0 text-muted" style="font-size:0.75rem;">
                                            Qty: <?php echo e($data['quantity']); ?> &bull; Reorder at: <?php echo e($data['reorder_level']); ?>

                                        </p>
                                    </div>
                                    <div class="d-flex flex-column align-items-end gap-1">
                                        <span class="text-muted" style="font-size:0.7rem;">
                                            <?php echo e($notif->created_at->diffForHumans()); ?>

                                        </span>
                                        <form method="POST" action="<?php echo e(route('notifications.read', $notif->id)); ?>">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="btn btn-link btn-sm p-0 text-muted"
                                                    style="font-size:0.7rem;" title="Dismiss">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <div class="mt-1">
                                    <a href="<?php echo e(route('products.show', $data['product_id'])); ?>"
                                       class="btn btn-outline-secondary btn-sm py-0 px-2"
                                       style="font-size:0.7rem;">
                                        View Product
                                    </a>
                                </div>
                            </div>
                        </li>
                        <li><hr class="dropdown-divider my-0"></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <li>
                            <p class="text-center text-muted small py-3 mb-0">
                                <i class="bi bi-check-circle me-1"></i> All caught up!
                            </p>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <span class="navbar-text text-white-50 small">Hi, <?php echo e(Auth::user()->name ?? 'User'); ?> 👋</span>
                <form method="POST" action="<?php echo e(route('logout')); ?>">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn btn-sm btn-outline-light">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

<!-- ===== FLASH MESSAGES ===== -->
<div class="container-fluid mt-3">
    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
</div>

<!-- ===== PAGE CONTENT ===== -->
<main class="container-fluid py-2">
    <?php echo $__env->yieldContent('content'); ?>
</main>

<!-- ===== FOOTER ===== -->
<footer class="site-footer text-center">
    &copy; <?php echo e(date('Y')); ?> KyleHardware Inventory System &mdash; Built with Laravel &amp; Bootstrap 5
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<?php echo $__env->yieldContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\Lab6_2411600237\inventory\resources\views/layouts/app.blade.php ENDPATH**/ ?>