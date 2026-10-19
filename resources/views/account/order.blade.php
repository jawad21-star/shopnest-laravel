@php
    $steps = ['pending', 'processing', 'shipped', 'delivered'];
    $current = array_search($order->status, $steps);
@endphp

<x-layouts.shop :title="'Order '.$order->order_number">
    <div class="container-shop py-12">
        <a href="{{ route('account') }}" class="text-sm font-semibold text-brand-600 hover:text-brand-700">← Back to orders</a>

        <div class="mt-4 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="font-mono text-2xl font-extrabold text-slate-900">{{ $order->order_number }}</h1>
                <p class="mt-1 text-sm text-slate-500">Placed on {{ $order->created_at->format('d M Y, h:i A') }}</p>
            </div>
            @include('account.partials.status')
        </div>

        @if ($current !== false)
            <ol class="card mt-8 grid grid-cols-4 p-6">
                @foreach ($steps as $i => $step)
                    <li class="relative flex flex-col items-center text-center">
                        @if ($i > 0)
                            <span class="absolute top-4 right-1/2 h-0.5 w-full {{ $i <= $current ? 'bg-brand-600' : 'bg-slate-200' }}"></span>
                        @endif
                        <span class="relative flex size-8 items-center justify-center rounded-full text-xs font-bold {{ $i <= $current ? 'bg-brand-600 text-white' : 'bg-slate-200 text-slate-500' }}">
                            @if ($i < $current) <x-shop-icon name="check" class="size-4" /> @else {{ $i + 1 }} @endif
                        </span>
                        <span class="mt-2 text-xs font-medium sm:text-sm {{ $i <= $current ? 'text-slate-900' : 'text-slate-400' }}">{{ \App\Models\Order::STATUSES[$step] }}</span>
                    </li>
                @endforeach
            </ol>
        @endif

        <div class="mt-8 grid gap-8 lg:grid-cols-3">
            <div class="card p-6 lg:col-span-2">
                <h2 class="font-bold text-slate-900">Items</h2>
                <ul class="mt-4 divide-y divide-slate-100">
                    @foreach ($order->items as $item)
                        <li class="flex items-center gap-4 py-4">
                            <div class="size-16 shrink-0 rounded-xl border border-slate-200 bg-slate-50">
                                <x-product-image :src="$item->product?->image_url" :alt="$item->product_name" class="size-full p-1.5" />
                            </div>
                            <div class="min-w-0 flex-1">
                                @if ($item->product)
                                    <a href="{{ route('products.show', $item->product) }}" class="font-medium text-slate-900 hover:text-brand-600">{{ $item->product_name }}</a>
                                @else
                                    <p class="font-medium text-slate-900">{{ $item->product_name }}</p>
                                @endif
                                <p class="text-sm text-slate-500">{{ money($item->price) }} × {{ $item->quantity }}</p>
                            </div>
                            <p class="font-semibold whitespace-nowrap text-slate-900">{{ money($item->total) }}</p>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="space-y-6">
                <div class="card p-6">
                    <h2 class="font-bold text-slate-900">Summary</h2>
                    <dl class="mt-4 space-y-2 text-sm">
                        <div class="flex justify-between"><dt class="text-slate-500">Subtotal</dt><dd>{{ money($order->subtotal) }}</dd></div>
                        @if ($order->discount > 0)<div class="flex justify-between text-emerald-600"><dt>Discount ({{ $order->coupon_code }})</dt><dd>- {{ money($order->discount) }}</dd></div>@endif
                        <div class="flex justify-between"><dt class="text-slate-500">Shipping</dt><dd>{{ $order->shipping > 0 ? money($order->shipping) : 'Free' }}</dd></div>
                        <div class="flex justify-between border-t border-slate-200 pt-3 text-base font-bold text-slate-900"><dt>Total</dt><dd>{{ money($order->total) }}</dd></div>
                    </dl>
                </div>
                <div class="card p-6 text-sm">
                    <h2 class="font-bold text-slate-900">Delivery</h2>
                    <p class="mt-3 text-slate-600">{{ $order->customer_name }}<br>{{ $order->address }}<br>{{ $order->city }} {{ $order->postal_code }}<br>{{ $order->customer_phone }}</p>
                    <h2 class="mt-5 font-bold text-slate-900">Payment</h2>
                    <p class="mt-2 text-slate-600">{{ $order->payment_method_label }} · {{ \App\Models\Order::PAYMENT_STATUSES[$order->payment_status] ?? $order->payment_status }}</p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.shop>
