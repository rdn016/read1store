<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'product_sku',
        'quantity',
        'unit_price',
        'subtotal',
        'product_snapshot',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'product_snapshot' => 'array',
    ];

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($item) {
            // Calculate subtotal
            $item->subtotal = $item->quantity * $item->unit_price;

            // Create product snapshot if not set
            if (empty($item->product_snapshot) && $item->product) {
                $item->product_snapshot = [
                    'id' => $item->product->id,
                    'name' => $item->product->name,
                    'sku' => $item->product->sku,
                    'price' => $item->product->price,
                    'specifications' => $item->product->specifications,
                    'featured_image' => $item->product->featured_image,
                ];
            }

            // Set product name and sku from product if not set
            if (empty($item->product_name) && $item->product) {
                $item->product_name = $item->product->name;
            }
            if (empty($item->product_sku) && $item->product) {
                $item->product_sku = $item->product->sku;
            }
        });

        static::updating(function ($item) {
            // Recalculate subtotal
            $item->subtotal = $item->quantity * $item->unit_price;
        });

        static::saved(function ($item) {
            // Update order total
            $item->order->calculateTotal();
        });

        static::deleted(function ($item) {
            // Update order total
            $item->order->calculateTotal();
        });
    }

    /**
     * Get the order that owns the item.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    /**
     * Get the product.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get formatted unit price.
     */
    public function getFormattedUnitPriceAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->unit_price, 0, ',', '.');
    }

    /**
     * Get formatted subtotal.
     */
    public function getFormattedSubtotalAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->subtotal, 0, ',', '.');
    }
}
