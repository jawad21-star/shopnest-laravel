<x-layouts.shop title="Shopping cart">
    <div class="container-shop py-12">
        <h1 class="text-3xl font-extrabold tracking-tight text-slate-900">Shopping cart</h1>

        @if ($cart->isEmpty())
            <div class="card mt-8 flex flex-col items-center px-6 py-20 text-center">
                <span class="flex size-20 items-center justify-center rounded-full bg-slate-100 text-slate-400"><x-shop-icon name="bag" class="size-9" /></span>
                <h2 class="mt-6 text-xl font-semibold text-slate-900">Your cart is empty</h2>
                <p class="mt-2 text-slate-500">Looks like you haven't added anything yet.</p>
                <a href="{{ route('shop') }}" class="btn-primary mt-8 !px-8 !py-3">Start shopping</a>
            </div>
        @else
            <div class="mt-8 grid gap-10 lg:grid-cols-3">
                <div class="lg:col-span-2">
                    <ul class="divide-y divide-slate-200 border-y border-slate-200">
                        @foreach ($cart->lines() as $line)
                            <li class="flex gap-4 py-6 sm:gap-6">
                                <a href="{{ route('products.show', $line->product) }}" class="size-24 shrink-0 overflow-hidden rounded-2xl border border-slate-200 bg-slate-50 sm:size-28">
                                    <x-product-image :src="$line->product->image_url" :alt="$line->product->name" class="size-full p-2" />
                                </a>
                                <div class="flex min-w-0 flex-1 flex-col">
                                    <div class="flex justify-between gap-4">
                                        <div class="min-w-0">
                                            <a href="{{ route('products.show', $line->product) }}" class="line-clamp-2 font-semibold text-slate-900 hover:text-brand-600">{{ $line->product->name }}</a>
                                            <p class="mt-1 text-sm text-slate-500">{{ money($line->product->price) }} each</p>
                                        </div>
                                        <p class="font-bold whitespace-nowrap text-slate-900">{{ money($line->total) }}</p>
                                    </div>
                                    <div class="mt-auto flex items-center justify-between pt-3">
                                        <form action="{{ route('cart.update', $line->product->id) }}" method="POST" class="flex items-center rounded-full border border-slate-300">
                                            @csrf @method('PATCH')
                                            <button name="quantity" value="{{ $line->quantity - 1 }}" class="flex size-9 items-center justify-center text-slate-600 hover:text-slate-900" aria-label="Decrease"><x-shop-icon name="minus" class="size-4" /></button>
                                            <span class="w-8 text-center text-sm font-semibold">{{ $line->quantity }}</span>
                                            <button name="quantity" value="{{ $line->quantity + 1 }}" @disabled($line->quantity >= $line->product->stock) class="flex size-9 items-center justify-center text-slate-600 hover:text-slate-900 disabled:opacity-30" aria-label="Increase"><x-shop-icon name="plus" class="size-4" /></button>
                                        </form>
                                        <form action="{{ route('cart.remove', $line->product->id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button class="flex items-center gap-1.5 text-sm font-medium text-slate-500 hover:text-rose-600"><x-shop-icon name="trash" class="size-4" /> Remove</button>
                                        </form>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('shop') }}" class="mt-6 inline-flex items-center gap-1 text-sm font-semibold text-brand-600 hover:text-brand-700">← Continue shopping</a>
                </div>

                <div class="lg:sticky lg:top-28 lg:self-start">
                    <div class="card bg-slate-50 p-6">
                        <h2 class="text-lg font-bold text-slate-900">Order summary</h2>

                        <div class="mt-5">
                            @if ($cart->coupon())
                                <div class="flex items-center justify-between rounded-xl border border-dashed border-emerald-300 bg-emerald-50 px-4 py-3">
                                    <p class="flex items-center gap-2 text-sm font-semibold text-emerald-700"><x-shop-icon name="tag" class="size-4" /> {{ $cart->coupon()->code }}</p>
                                    <form action="{{ route('cart.coupon.remove') }}" method="POST">@csrf @method('DELETE')<button class="text-xs font-semibold text-slate-500 hover:text-rose-600">Remove</button></form>
                                </div>
                                @if ($cart->discount() == 0)
                                    <p class="mt-2 text-xs text-amber-600">{{ $cart->coupon()->validationError($cart->subtotal()) }}</p>
                                @endif
                            @else
                                <form action="{{ route('cart.coupon.apply') }}" method="POST" class="flex gap-2">
                                    @csrf
                                    <input name="code" value="{{ old('code') }}" placeholder="Coupon code" class="input !rounded-full uppercase">
                                    <button class="btn-outline shrink-0">Apply</button>
                                </form>
                                @error('code')<p class="mt-2 text-xs text-rose-600">{{ $message }}</p>@enderror
                            @endif
                        </div>

                        <div class="mt-6">@include('shop.partials.summary')</div>

                        <a href="{{ route('checkout') }}" class="btn-primary mt-6 w-full !py-3.5">Proceed to checkout <x-shop-icon name="arrow-right" class="size-4" /></a>
                        <p class="mt-4 flex items-center justify-center gap-1.5 text-xs text-slate-500"><x-shop-icon name="shield" class="size-4" /> Secure checkout</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-layouts.shop>
