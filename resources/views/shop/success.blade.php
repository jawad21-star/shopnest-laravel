<x-layouts.shop title="Order confirmed">
    <div class="container-shop max-w-3xl py-16">
        <div class="text-center">
            <span class="mx-auto flex size-20 animate-fade-up items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                <x-shop-icon name="check" class="size-10" />
            </span>
            <h1 class="mt-6 text-3xl font-extrabold tracking-tight text-slate-900">Thank you for your order!</h1>
            <p class="mt-2 text-slate-500">We've received your order and will contact you at <strong class="text-slate-700">{{ $order->customer_phone }}</strong> to confirm.</p>
            <p class="mt-4 inline-flex items-center gap-2 rounded-full bg-slate-100 px-4 py-2 text-sm">Order number: <strong class="font-mono text-slate-900">{{ $order->order_number }}</strong></p>
        </div>

        @if ($order->payment_method === 'bank')
            <div class="mt-10 rounded-2xl border border-amber-200 bg-amber-50 p-6">
                <h2 class="font-semibold text-amber-900">Bank transfer details</h2>
                <dl class="mt-3 grid gap-2 text-sm text-amber-900 sm:grid-cols-2">
                    <div><dt class="text-amber-700">Bank</dt><dd class="font-medium">Meezan Bank</dd></div>
                    <div><dt class="text-amber-700">Account title</dt><dd class="font-medium">{{ config('shop.name') }}</dd></div>
                    <div><dt class="text-amber-700">IBAN</dt><dd class="font-mono font-medium">PK00 MEZN 0000 0000 0000 0000</dd></div>
                    <div><dt class="text-amber-700">Reference</dt><dd class="font-mono font-medium">{{ $order->order_number }}</dd></div>
                </dl>
                <p class="mt-3 text-xs text-amber-700">Please use your order number as the payment reference.</p>
            </div>
        @endif

        <div class="card mt-10 p-6">
            <h2 class="font-bold text-slate-900">Order details</h2>
            <ul class="mt-4 divide-y divide-slate-100">
                @foreach ($order->items as $item)
                    <li class="flex justify-between gap-4 py-3 text-sm">
                        <span class="text-slate-700">{{ $item->product_name }} <span class="text-slate-400">× {{ $item->quantity }}</span></span>
                        <span class="font-medium whitespace-nowrap text-slate-900">{{ money($item->total) }}</span>
                    </li>
                @endforeach
            </ul>
            <dl class="mt-2 space-y-2 border-t border-slate-200 pt-4 text-sm">
                <div class="flex justify-between"><dt class="text-slate-500">Subtotal</dt><dd>{{ money($order->subtotal) }}</dd></div>
                @if ($order->discount > 0)<div class="flex justify-between text-emerald-600"><dt>Discount</dt><dd>- {{ money($order->discount) }}</dd></div>@endif
                <div class="flex justify-between"><dt class="text-slate-500">Shipping</dt><dd>{{ $order->shipping > 0 ? money($order->shipping) : 'Free' }}</dd></div>
                <div class="flex justify-between pt-2 text-base font-bold text-slate-900"><dt>Total</dt><dd>{{ money($order->total) }}</dd></div>
            </dl>
            <div class="mt-6 grid gap-4 border-t border-slate-200 pt-6 text-sm sm:grid-cols-2">
                <div>
                    <p class="font-semibold text-slate-900">Shipping to</p>
                    <p class="mt-1 text-slate-600">{{ $order->customer_name }}<br>{{ $order->address }}<br>{{ $order->city }} {{ $order->postal_code }}</p>
                </div>
                <div>
                    <p class="font-semibold text-slate-900">Payment</p>
                    <p class="mt-1 text-slate-600">{{ $order->payment_method_label }}</p>
                </div>
            </div>
        </div>

        <div class="mt-8 flex flex-wrap justify-center gap-3">
            <a href="{{ route('shop') }}" class="btn-primary">Continue shopping</a>
            @auth<a href="{{ route('account') }}" class="btn-outline">View my orders</a>@endauth
        </div>
    </div>
</x-layouts.shop>
