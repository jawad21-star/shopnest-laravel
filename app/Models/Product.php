<?php

namespace App\Models;

use App\Models\Concerns\HasImageUrl;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasImageUrl;

    protected $fillable = [
        'category_id', 'brand_id', 'name', 'slug', 'sku', 'short_description', 'description',
        'price', 'compare_price', 'stock', 'image', 'gallery', 'is_active', 'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'compare_price' => 'decimal:2',
            'gallery' => 'array',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews(): HasMany
    {
        return $this->reviews()->where('is_approved', true);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    public function getImageUrlAttribute(): ?string
    {
        return static::resolveImageUrl($this->image);
    }

    /** @return list<string> */
    public function getGalleryUrlsAttribute(): array
    {
        return collect([$this->image, ...($this->gallery ?? [])])
            ->filter()
            ->unique()
            ->map(fn (string $path) => static::resolveImageUrl($path))
            ->values()
            ->all();
    }

    public function getDiscountPercentAttribute(): ?int
    {
        if (! $this->compare_price || $this->compare_price <= $this->price) {
            return null;
        }

        return (int) round((1 - $this->price / $this->compare_price) * 100);
    }

    public function getInStockAttribute(): bool
    {
        return $this->stock > 0;
    }
}
