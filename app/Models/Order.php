<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    public const STATUSES = [
        'pending' => 'Pending',
        'processing' => 'Processing',
        'shipped' => 'Shipped',
        'delivered' => 'Delivered',
        'cancelled' => 'Cancelled',
    ];

    public const PAYMENT_METHODS = [
        'cod' => 'Cash on Delivery',
        'bank' => 'Bank Transfer',
    ];

    public const PAYMENT_STATUSES = [
        'unpaid' => 'Unpaid',
        'paid' => 'Paid',
        'refunded' => 'Refunded',
    ];

    protected $fillable = [
        'user_id', 'order_number', 'status', 'payment_method', 'payment_status',
        'subtotal', 'discount', 'shipping', 'total', 'coupon_code',
        'customer_name', 'customer_email', 'customer_phone', 'address', 'city', 'postal_code', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'shipping' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            $order->order_number ??= 'ORD-'.now()->format('ymd').'-'.Str::upper(Str::random(5));
        });

        // Return stock when an order is cancelled, and take it again if it is re-opened.
        static::updated(function (Order $order) {
            if (! $order->wasChanged('status')) {
                return;
            }

            $wasCancelled = $order->getOriginal('status') === 'cancelled';
            $isCancelled = $order->status === 'cancelled';

            if ($wasCancelled !== $isCancelled) {
                foreach ($order->items()->whereNotNull('product_id')->get() as $item) {
                    $isCancelled
                        ? Product::whereKey($item->product_id)->increment('stock', $item->quantity)
                        : Product::whereKey($item->product_id)->where('stock', '>=', $item->quantity)->decrement('stock', $item->quantity);
                }
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'order_number';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? Str::headline($this->status);
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return self::PAYMENT_METHODS[$this->payment_method] ?? Str::headline($this->payment_method);
    }
}
