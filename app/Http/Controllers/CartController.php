<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Product;
use App\Services\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private Cart $cart) {}

    public function index()
    {
        return view('shop.cart', ['cart' => $this->cart]);
    }

    public function add(Request $request, Product $product)
    {
        abort_unless($product->is_active, 404);

        if (! $product->in_stock) {
            return back()->with('error', 'Sorry, this product is out of stock.');
        }

        $this->cart->add($product, (int) $request->input('quantity', 1));

        if ($request->expectsJson()) {
            return response()->json(['count' => $this->cart->count(), 'message' => "{$product->name} added to cart."]);
        }

        if ($request->boolean('buy_now')) {
            return redirect()->route('checkout');
        }

        return back()->with('success', "{$product->name} added to cart.");
    }

    public function update(Request $request, int $productId)
    {
        $this->cart->update($productId, (int) $request->input('quantity'));

        return back();
    }

    public function remove(int $productId)
    {
        $this->cart->remove($productId);

        return back()->with('success', 'Item removed from cart.');
    }

    public function applyCoupon(Request $request)
    {
        $request->validate(['code' => ['required', 'string', 'max:50']]);

        $coupon = Coupon::where('code', strtoupper(trim($request->input('code'))))->first();

        if (! $coupon) {
            return back()->withErrors(['code' => 'Invalid coupon code.']);
        }

        if ($error = $coupon->validationError($this->cart->subtotal())) {
            return back()->withErrors(['code' => $error]);
        }

        $this->cart->applyCoupon($coupon);

        return back()->with('success', "Coupon {$coupon->code} applied.");
    }

    public function removeCoupon()
    {
        $this->cart->removeCoupon();

        return back();
    }
}
