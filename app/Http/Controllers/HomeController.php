<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function __invoke()
    {
        return view('shop.home', [
            'categories' => Category::active()->whereNull('parent_id')->withCount(['products' => fn ($q) => $q->active()])->orderBy('sort_order')->take(8)->get(),
            'featured' => Product::active()->where('is_featured', true)->with('category')->latest()->take(8)->get(),
            'newArrivals' => Product::active()->with('category')->latest()->take(8)->get(),
            'brands' => Brand::active()->orderBy('name')->get(),
        ]);
    }
}
