<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Services\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    public function __construct(private Cart $cart) {}

    public function index()
    {
        if ($this->cart->isEmpty()) {
            return redirect()->route('cart.index');
        }

        return view('shop.checkout', ['cart' => $this->cart, 'user' => auth()->user()]);
    }

    public function store(Request $request)
    {
        if ($this->cart->isEmpty()) {
            return redirect()->route('cart.index');
        }

        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:30'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'payment_method' => ['required', Rule::in(array_keys(Order::PAYMENT_METHODS))],
        ]);

        $order = DB::transaction(function () use ($data) {
            // Lock the products so two checkouts can't oversell the same stock.
            $lines = $this->cart->lines();
            $products = Product::whereIn('id', $lines->pluck('product.id'))->lockForUpdate()->get()->keyBy('id');

            foreach ($lines as $line) {
                if ($products[$line->product->id]->stock < $line->quantity) {
                    throw ValidationException::withMessages([
                        'cart' => "Only {$products[$line->product->id]->stock} left of {$line->product->name}. Please update your cart.",
                    ]);
                }
            }

            $coupon = $this->cart->coupon();
            $discount = $this->cart->discount();

            $order = Order::create([
                ...$data,
                'user_id' => auth()->id(),
                'subtotal' => $this->cart->subtotal(),
                'discount' => $discount,
                'shipping' => $this->cart->shipping(),
                'total' => $this->cart->total(),
                'coupon_code' => $discount > 0 ? $coupon?->code : null,
            ]);

            foreach ($lines as $line) {
                $order->items()->create([
                    'product_id' => $line->product->id,
                    'product_name' => $line->product->name,
                    'price' => $line->product->price,
                    'quantity' => $line->quantity,
                    'total' => $line->total,
                ]);

                $products[$line->product->id]->decrement('stock', $line->quantity);
            }

            if ($discount > 0) {
                $coupon->increment('used_count');
            }

            return $order;
        });

        $this->cart->clear();

        $user = auth()->user();
        if ($user && ! $user->phone) {
            $user->update(['phone' => $data['customer_phone'], 'address' => $data['address'], 'city' => $data['city']]);
        }

        session()->put('last_order', $order->order_number);

        return redirect()->route('checkout.success', $order);
    }

    public function success(Order $order)
    {
        // Only the buyer (by session or account) may see the confirmation page.
        abort_unless(session('last_order') === $order->order_number || ($order->user_id && $order->user_id === auth()->id()), 404);

        return view('shop.success', ['order' => $order->load('items')]);
    }
}
