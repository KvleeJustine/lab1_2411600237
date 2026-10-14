<?php

namespace App\Observers;

use App\Models\InventoryTransaction;
use App\Models\User;
use App\Notifications\LowStockNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventoryTransactionObserver
{
    /**
     * Handle the InventoryTransaction "created" event.
     *
     * Updates the related product's quantity inside a DB transaction so both
     * writes succeed or both are rolled back (atomicity).
     *
     * Transaction type behaviour:
     *  - 'in'         → quantity increases (receiving goods / purchase order)
     *  - 'out'        → quantity decreases (sales / issue to project)
     *  - 'adjustment' → quantity is SET to the recorded value (physical count correction)
     *                   The quantity field stores the new physical count for adjustments.
     */
    public function created(InventoryTransaction $transaction): void
    {
        DB::transaction(function () use ($transaction) {
            // Row-level lock prevents race conditions on concurrent requests
            $product = \App\Models\Product::lockForUpdate()
                ->find($transaction->product_id);

            if (! $product) {
                throw new \Exception(
                    "Product #{$transaction->product_id} not found during stock update."
                );
            }

            switch ($transaction->type) {
                case 'in':
                    $product->quantity += $transaction->quantity;
                    break;

                case 'out':
                    // Prevent negative inventory (Option A: throw and rollback)
                    if ($transaction->quantity > $product->quantity) {
                        throw new \Exception(
                            "Insufficient stock for \"{$product->name}\". " .
                            "Available: {$product->quantity}, " .
                            "Requested: {$transaction->quantity}."
                        );
                    }
                    $product->quantity -= $transaction->quantity;
                    break;

                case 'adjustment':
                    // For adjustments, quantity in the transaction represents the
                    // DIFFERENCE (positive = found more, negative not applicable here
                    // since the field is unsigned — so we treat positive as add,
                    // but the controller stores the physical-count difference).
                    // We ADD the adjustment quantity to current stock.
                    // A negative physical difference should be recorded as a
                    // stock-out instead; adjustments here correct upward discrepancies.
                    $product->quantity += $transaction->quantity;
                    // Guard: never go below zero even on adjustment
                    if ($product->quantity < 0) {
                        $product->quantity = 0;
                    }
                    break;
            }

            $product->save();

            // Trigger low-stock notification if the product has just gone critical
            if ($product->isLowStock() || $product->isOutOfStock()) {
                $this->notifyLowStock($product);
            }
        });
    }

    /**
     * Send a LowStockNotification to all registered users.
     * Errors are swallowed so a notification failure never breaks the
     * main inventory transaction.
     */
    private function notifyLowStock(\App\Models\Product $product): void
    {
        try {
            $users = User::all();
            foreach ($users as $user) {
                $user->notify(new LowStockNotification($product));
            }
        } catch (\Throwable $e) {
            Log::error('LowStockNotification failed: ' . $e->getMessage());
        }
    }
}
