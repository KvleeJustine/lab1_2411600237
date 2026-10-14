<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sku',
        'description',
        'category',
        'quantity',
        'reorder_level',
        'unit_price',
        'supplier',
    ];

    protected $casts = [
        'quantity'      => 'integer',
        'reorder_level' => 'integer',
        'unit_price'    => 'decimal:2',
    ];

    // ── Relationships ────────────────────────────────────────────────────────

    /** A product has many inventory transactions */
    public function transactions()
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    /** The single most recent transaction for this product */
    public function latestTransaction()
    {
        return $this->hasOne(InventoryTransaction::class)->latestOfMany();
    }

    // ── Business Logic Helpers ───────────────────────────────────────────────

    /** True if quantity is at or below reorder level but not zero */
    public function isLowStock(): bool
    {
        return $this->quantity <= $this->reorder_level && $this->quantity > 0;
    }

    /** True if quantity is zero or negative */
    public function isOutOfStock(): bool
    {
        return $this->quantity <= 0;
    }

    /** Human-readable stock status label */
    public function stockStatus(): string
    {
        if ($this->isOutOfStock()) return 'Out of Stock';
        if ($this->isLowStock())   return 'Low Stock';
        return 'In Stock';
    }

    /** Bootstrap badge CSS class based on stock status */
    public function stockBadgeClass(): string
    {
        if ($this->isOutOfStock()) return 'badge-out';
        if ($this->isLowStock())   return 'badge-low-stock';
        return 'badge-in-stock';
    }

    // ── Accessors (Attribute style) ──────────────────────────────────────────

    /** Returns 'In Stock', 'Low Stock', or 'Out of Stock' */
    public function getStockStatusAttribute(): string
    {
        return $this->stockStatus();
    }

    /** Returns Bootstrap color name: success, warning, danger */
    public function getStockStatusColorAttribute(): string
    {
        if ($this->isOutOfStock()) return 'danger';
        if ($this->isLowStock())   return 'warning';
        return 'success';
    }

    // ── Query Scopes ─────────────────────────────────────────────────────────

    /** Products at or below reorder level (including zero) */
    public function scopeLowStock($query)
    {
        return $query->whereRaw('quantity <= reorder_level')->where('quantity', '>', 0);
    }

    /** Products with zero or negative quantity */
    public function scopeOutOfStock($query)
    {
        return $query->where('quantity', '<=', 0);
    }

    /** Products in a specific category */
    public function scopeInCategory($query, string $category)
    {
        return $query->where('category', $category);
    }
}
