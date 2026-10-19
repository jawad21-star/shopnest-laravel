@php
    $subtotal = $cart->subtotal();
    $remaining = config('shop.free_shipping_over') - $subtotal;
@endphp

<dl class="space-y-3 text-sm">
    <div class="flex justify-between"><dt class="text-slate-500">Subtotal</dt><dd class="font-medium text-slate-900">{{ money($subtotal) }}</dd></div>
    @if ($cart->discount() > 0)
        <div class="flex justify-between text-emerald-600"><dt>Discount ({{ $cart->coupon()->code }})</dt><dd class="font-medium">- {{ money($cart->discount()) }}</dd></div>
    @endif
    <div class="flex justify-between">
        <dt class="text-slate-500">Shipping</dt>
        <dd class="font-medium text-slate-900">{{ $cart->shipping() > 0 ? money($cart->shipping()) : 'Free' }}</dd>
    </div>
    <div class="flex justify-between border-t border-slate-200 pt-4 text-base">
        <dt class="font-semibold text-slate-900">Total</dt><dd class="text-xl font-extrabold text-slate-900">{{ money($cart->total()) }}</dd>
    </div>
</dl>

@if ($remaining > 0)
    <div class="mt-5 rounded-xl bg-brand-50 p-3.5">
        <p class="text-xs font-medium text-brand-700">Add <strong>{{ money($remaining) }}</strong> more for free delivery</p>
        <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-brand-100">
            <div class="h-full rounded-full bg-brand-600" style="width: {{ min(100, $subtotal / config('shop.free_shipping_over') * 100) }}%"></div>
        </div>
    </div>
@endif
