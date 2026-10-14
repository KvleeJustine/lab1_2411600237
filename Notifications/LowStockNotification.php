<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification
{
    use Queueable;

    public function __construct(public readonly Product $product) {}

    /**
     * Delivery channels — we use the database channel so notifications
     * appear in the UI bell without requiring an external mail service.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Data stored in the notifications.data JSON column.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'product_id'    => $this->product->id,
            'product_name'  => $this->product->name,
            'product_sku'   => $this->product->sku,
            'quantity'      => $this->product->quantity,
            'reorder_level' => $this->product->reorder_level,
            'status'        => $this->product->stock_status,
            'message'       => "\"{$this->product->name}\" is {$this->product->stock_status} "
                             . "(qty: {$this->product->quantity}, reorder at: {$this->product->reorder_level}).",
        ];
    }
}
