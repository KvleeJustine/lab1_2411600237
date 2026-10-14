<?php

namespace App\Providers;

use App\Models\InventoryTransaction;
use App\Observers\InventoryTransactionObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Register the observer so stock quantities are updated automatically
        // whenever an InventoryTransaction is created.
        InventoryTransaction::observe(InventoryTransactionObserver::class);
    }
}
