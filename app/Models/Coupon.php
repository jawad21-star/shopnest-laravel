<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = ['code', 'type', 'value', 'min_amount', 'max_uses', 'used_count', 'expires_at', 'is_active'];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'min_amount' => 'decimal:2',
            'expires_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    /** Returns an error message, or null when the coupon can be applied to the given subtotal. */
    public function validationError(float $subtotal): ?string
    {
        return match (true) {
            ! $this->is_active => 'This coupon is not active.',
            $this->expires_at?->isPast() ?? false => 'This coupon has expired.',
            $this->max_uses !== null && $this->used_count >= $this->max_uses => 'This coupon has reached its usage limit.',
            $this->min_amount !== null && $subtotal < (float) $this->min_amount => 'Minimum order of '.money($this->min_amount).' required for this coupon.',
            default => null,
        };
    }

    public function discountFor(float $subtotal): float
    {
        $discount = $this->type === 'percent'
            ? $subtotal * ((float) $this->value / 100)
            : (float) $this->value;

        return round(min($discount, $subtotal), 2);
    }
}
