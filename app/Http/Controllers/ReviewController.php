<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $data = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        $product->reviews()->updateOrCreate(
            ['user_id' => $request->user()->id],
            [...$data, 'is_approved' => false],
        );

        return back()->with('success', 'Thanks! Your review will appear once approved.');
    }
}
