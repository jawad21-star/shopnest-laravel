<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index(Request $request)
    {
        return view('account.wishlist', [
            'products' => $request->user()->wishlist()->active()->latest('wishlists.created_at')->get(),
        ]);
    }

    public function toggle(Request $request, Product $product)
    {
        $result = $request->user()->wishlist()->toggle($product->id);
        $added = ! empty($result['attached']);

        if ($request->expectsJson()) {
            return response()->json(['added' => $added]);
        }

        return back()->with('success', $added ? 'Added to your wishlist.' : 'Removed from your wishlist.');
    }
}
