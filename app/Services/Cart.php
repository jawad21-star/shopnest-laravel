<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Support\Collection;

/**
 * Session-backed shopping cart. Stores only product ids and quantities;
 * prices are always read fresh from the database.
 */
class Cart
{
    private const KEY = 'cart.items';

    private const COUPON_KEY = 'cart.coupon';

    private ?Collection $lines = null;

    /** @return array<int, int> product id => quantity */
    public function raw(): array
    {
        return session(self::KEY, []);
    }

    public function add(Product $product, int $quantity = 1): void
    {
        $items = $this->raw();
        $items[$product->id] = min(($items[$product->id] ?? 0) + max(1, $quantity), $product->stock);
        $this->save($items);
    }

    public function update(int $productId, int $quantity): void
    {
        $items = $this->raw();

        if ($quantity <= 0) {
            unset($items[$productId]);
        } else {
            $stock = Product::whereKey($productId)->value('stock') ?? 0;
            $items[$productId] = min($quantity, $stock);
        }

        $this->save(array_filter($items));
    }

    public function remove(int $productId): void
    {
        $items = $this->raw();
        unset($items[$productId]);
        $this->save($items);
    }

    public function clear(): void
    {
        session()->forget([self::KEY, self::COUPON_KEY]);
        $this->lines = null;
    }

    public function count(): int
    {
        return array_sum($this->raw());
    }

    public function isEmpty(): bool
    {
        return $this->lines()->isEmpty();
    }

    /** @return Collection<int, object{product: Product, quantity: int, total: float}> */
    public function lines(): Collection
    {
        if ($this->lines !== null) {
            return $this->lines;
        }

        $items = $this->raw();
        $products = Product::active()->whereIn('id', array_keys($items))->get()->keyBy('id');

        return $this->lines = collect($items)
            ->filter(fn ($qty, $id) => $products->has($id))
            ->map(fn ($qty, $id) => (object) [
                'product' => $products[$id],
                'quantity' => $qty,
                'total' => (float) $products[$id]->price * $qty,
            ])
            ->values();
    }

    public function subtotal(): float
    {
        return round($this->lines()->sum('total'), 2);
    }

    public function coupon(): ?Coupon
    {
        $code = session(self::COUPON_KEY);

        return $code ? Coupon::where('code', $code)->first() : null;
    }

    public function applyCoupon(Coupon $coupon): void
    {
        session([self::COUPON_KEY => $coupon->code]);
    }

    public function removeCoupon(): void
    {
        session()->forget(self::COUPON_KEY);
    }

    public function discount(): float
    {
        $coupon = $this->coupon();
        $subtotal = $this->subtotal();

        if (! $coupon || $coupon->validationError($subtotal)) {
            return 0;
        }

        return $coupon->discountFor($subtotal);
    }

    public function shipping(): float
    {
        $subtotal = $this->subtotal();

        if ($subtotal <= 0 || $subtotal >= config('shop.free_shipping_over')) {
            return 0;
        }

        return (float) config('shop.shipping_fee');
    }

    public function total(): float
    {
        return round(max(0, $this->subtotal() - $this->discount()) + $this->shipping(), 2);
    }

    private function save(array $items): void
    {
        session([self::KEY => $items]);
        $this->lines = null;
    }
}
