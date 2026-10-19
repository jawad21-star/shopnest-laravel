<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        return view('account.index', [
            'user' => $request->user(),
            'orders' => $request->user()->orders()->withCount('items')->latest()->paginate(10),
        ]);
    }

    public function order(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 404);

        return view('account.order', ['order' => $order->load('items.product')]);
    }

    public function updateProfile(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
        ]);

        $request->user()->update($data);

        return back()->with('success', 'Profile updated.');
    }
}
