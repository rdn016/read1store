<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_whatsapp',
        'shipping_address',
        'total_amount',
        'status',
        'notes',
        'admin_notes',
        'confirmed_at',
        'shipped_at',
        'completed_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'confirmed_at' => 'datetime',
        'shipped_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Available order statuses.
     */
    public const STATUSES = [
        'pending' => 'Pending',
        'confirmed' => 'Confirmed',
        'processing' => 'Processing',
        'shipped' => 'Shipped',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ];

    /**
     * Status colors for badges.
     */
    public const STATUS_COLORS = [
        'pending' => 'warning',
        'confirmed' => 'info',
        'processing' => 'primary',
        'shipped' => 'success',
        'completed' => 'success',
        'cancelled' => 'danger',
    ];

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = self::generateOrderNumber();
            }
        });
    }

    /**
     * Generate a unique order number.
     */
    public static function generateOrderNumber(): string
    {
        $prefix = 'ORD';
        $date = now()->format('Ymd');
        $sequence = self::whereDate('created_at', today())->count() + 1;

        return sprintf('%s-%s-%04d', $prefix, $date, $sequence);
    }

    /**
     * Get the user that owns the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the items for this order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Scope a query to filter by status.
     */
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include pending orders.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include confirmed orders.
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope a query to only include completed orders.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Get the status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    /**
     * Get the status color.
     */
    public function getStatusColorAttribute(): string
    {
        return self::STATUS_COLORS[$this->status] ?? 'secondary';
    }

    /**
     * Get formatted total amount.
     */
    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->total_amount, 0, ',', '.');
    }

    /**
     * Calculate and update total from items.
     */
    public function calculateTotal(): void
    {
        $this->total_amount = $this->items->sum('subtotal');
        $this->save();
    }

    /**
     * Confirm the order and decrement stock.
     */
    public function confirm(): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        // Decrement stock for each item
        foreach ($this->items as $item) {
            if ($item->product) {
                $item->product->decrementStock($item->quantity);
            }
        }

        $this->status = 'confirmed';
        $this->confirmed_at = now();
        $this->save();

        return true;
    }

    /**
     * Cancel the order and restore stock if was confirmed.
     */
    public function cancel(): bool
    {
        if ($this->status === 'cancelled' || $this->status === 'completed') {
            return false;
        }

        // Restore stock if order was confirmed
        if (in_array($this->status, ['confirmed', 'processing', 'shipped'])) {
            foreach ($this->items as $item) {
                if ($item->product) {
                    $item->product->incrementStock($item->quantity);
                }
            }
        }

        $this->status = 'cancelled';
        $this->save();

        return true;
    }

    /**
     * Get WhatsApp link for this order.
     */
    public function getWhatsappLinkAttribute(): string
    {
        $phone = preg_replace('/[^0-9]/', '', $this->customer_whatsapp);

        // Convert local format to international
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        $message = $this->generateWhatsappMessage();

        return "https://wa.me/{$phone}?text=" . urlencode($message);
    }

    /**
     * Generate WhatsApp message for this order.
     */
    protected function generateWhatsappMessage(): string
    {
        $lines = [
            "Halo {$this->customer_name}! 👋",
            "",
            "Terima kasih telah memesan di Read1 Store.",
            "Berikut detail pesanan Anda:",
            "",
            "📦 Order: {$this->order_number}",
            "📅 Tanggal: " . $this->created_at->format('d M Y H:i'),
            "",
            "Produk:",
        ];

        foreach ($this->items as $item) {
            $lines[] = "• {$item->product_name} x{$item->quantity} - Rp " . number_format((float) $item->subtotal, 0, ',', '.');
        }

        $lines[] = "";
        $lines[] = "💰 Total: {$this->formatted_total}";
        $lines[] = "";
        $lines[] = "Status: " . strtoupper($this->status_label);

        return implode("\n", $lines);
    }

    /**
     * Check if order can be confirmed.
     */
    public function canBeConfirmed(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if order can be cancelled.
     */
    public function canBeCancelled(): bool
    {
        return !in_array($this->status, ['cancelled', 'completed']);
    }
}
