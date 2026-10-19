<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request, ?Category $category = null)
    {
        $query = Product::active()->with('category')->withAvg('approvedReviews as rating', 'rating');

        if ($category) {
            $query->whereIn('category_id', $category->children()->pluck('id')->push($category->id));
        } elseif ($request->filled('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $request->string('category')));
        }

        if ($request->filled('brands')) {
            $query->whereHas('brand', fn ($q) => $q->whereIn('slug', (array) $request->input('brands')));
        }

        if ($search = trim((string) $request->input('q'))) {
            $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('short_description', 'like', "%{$search}%")
                ->orWhere('sku', $search));
        }

        $query->when($request->filled('min'), fn ($q) => $q->where('price', '>=', (float) $request->input('min')))
            ->when($request->filled('max'), fn ($q) => $q->where('price', '<=', (float) $request->input('max')))
            ->when($request->boolean('in_stock'), fn ($q) => $q->where('stock', '>', 0))
            ->when($request->boolean('sale'), fn ($q) => $q->whereColumn('compare_price', '>', 'price'));

        match ($request->input('sort')) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'name' => $query->orderBy('name'),
            'popular' => $query->withSum('orderItems as sold', 'quantity')->orderByDesc('sold'),
            default => $query->latest(),
        };

        return view('shop.index', [
            'products' => $query->paginate(12)->withQueryString(),
            'category' => $category,
            'categories' => Category::active()->whereNull('parent_id')->withCount(['products' => fn ($q) => $q->active()])->orderBy('sort_order')->get(),
            'brands' => Brand::active()->orderBy('name')->get(),
        ]);
    }

    public function category(Request $request, Category $category)
    {
        abort_unless($category->is_active, 404);

        return $this->index($request, $category);
    }

    public function show(Product $product)
    {
        abort_unless($product->is_active, 404);

        $product->load(['category', 'brand', 'approvedReviews' => fn ($q) => $q->with('user')->latest()]);

        $related = Product::active()
            ->where('category_id', $product->category_id)
            ->whereKeyNot($product->id)
            ->inRandomOrder()
            ->take(4)
            ->get();

        $user = auth()->user();

        return view('shop.product', [
            'product' => $product,
            'related' => $related,
            'rating' => round((float) $product->approvedReviews->avg('rating'), 1),
            'inWishlist' => $user?->wishlist()->whereKey($product->id)->exists() ?? false,
            'canReview' => $user && ! $product->reviews()->where('user_id', $user->id)->exists(),
        ]);
    }
}
