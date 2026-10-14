<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'type',
        'quantity',
        'reference',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    // ── Relationships ────────────────────────────────────────────────────────

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Query Scopes ─────────────────────────────────────────────────────────

    /** Filter for stock-in transactions */
    public function scopeStockIn($query)
    {
        return $query->where('type', 'in');
    }

    /** Filter for stock-out transactions */
    public function scopeStockOut($query)
    {
        return $query->where('type', 'out');
    }

    /** Order newest first */
    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}
