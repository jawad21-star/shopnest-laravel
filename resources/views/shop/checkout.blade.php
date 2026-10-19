<x-layouts.shop title="Checkout">
    <div class="container-shop py-12">
        <h1 class="text-3xl font-extrabold tracking-tight text-slate-900">Checkout</h1>
        @guest
            <p class="mt-2 text-sm text-slate-500">Already have an account? <a href="{{ route('login') }}" class="font-semibold text-brand-600 hover:underline">Sign in</a> for faster checkout.</p>
        @endguest

        @error('cart')
            <div class="mt-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">{{ $message }} <a href="{{ route('cart.index') }}" class="font-semibold underline">Go to cart</a></div>
        @enderror

        <form action="{{ route('checkout.store') }}" method="POST" class="mt-8 grid gap-10 lg:grid-cols-5" x-data="{ payment: '{{ old('payment_method', 'cod') }}', submitting: false }" @submit="submitting = true">
            @csrf
            <div class="space-y-8 lg:col-span-3">
                <section class="card p-6">
                    <h2 class="flex items-center gap-3 text-lg font-bold text-slate-900"><span class="flex size-7 items-center justify-center rounded-full bg-slate-900 text-xs text-white">1</span> Contact information</h2>
                    <div class="mt-5 grid gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label class="label" for="customer_name">Full name</label>
                            <input id="customer_name" name="customer_name" value="{{ old('customer_name', $user?->name) }}" required class="input">
                            @error('customer_name')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="label" for="customer_email">Email</label>
                            <input id="customer_email" type="email" name="customer_email" value="{{ old('customer_email', $user?->email) }}" required class="input">
                            @error('customer_email')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="label" for="customer_phone">Phone</label>
                            <input id="customer_phone" type="tel" name="customer_phone" value="{{ old('customer_phone', $user?->phone) }}" placeholder="03xx xxxxxxx" required class="input">
                            @error('customer_phone')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </section>

                <section class="card p-6">
                    <h2 class="flex items-center gap-3 text-lg font-bold text-slate-900"><span class="flex size-7 items-center justify-center rounded-full bg-slate-900 text-xs text-white">2</span> Shipping address</h2>
                    <div class="mt-5 grid gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label class="label" for="address">Street address</label>
                            <input id="address" name="address" value="{{ old('address', $user?->address) }}" placeholder="House #, street, area" required class="input">
                            @error('address')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="label" for="city">City</label>
                            <input id="city" name="city" value="{{ old('city', $user?->city) }}" list="cities" required class="input">
                            <datalist id="cities">@foreach (['Lahore', 'Karachi', 'Islamabad', 'Rawalpindi', 'Faisalabad', 'Multan', 'Peshawar', 'Quetta', 'Sialkot', 'Hyderabad'] as $city)<option value="{{ $city }}">@endforeach</datalist>
                            @error('city')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="label" for="postal_code">Postal code <span class="font-normal text-slate-400">(optional)</span></label>
                            <input id="postal_code" name="postal_code" value="{{ old('postal_code') }}" class="input">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="label" for="notes">Order notes <span class="font-normal text-slate-400">(optional)</span></label>
                            <textarea id="notes" name="notes" rows="2" class="input" placeholder="Delivery instructions, landmark…">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </section>

                <section class="card p-6">
                    <h2 class="flex items-center gap-3 text-lg font-bold text-slate-900"><span class="flex size-7 items-center justify-center rounded-full bg-slate-900 text-xs text-white">3</span> Payment method</h2>
                    <div class="mt-5 space-y-3">
                        @foreach (\App\Models\Order::PAYMENT_METHODS as $value => $label)
                            <label class="flex cursor-pointer items-start gap-3 rounded-xl border-2 p-4 transition" :class="payment === '{{ $value }}' ? 'border-brand-600 bg-brand-50/50' : 'border-slate-200 hover:border-slate-300'">
                                <input type="radio" name="payment_method" value="{{ $value }}" x-model="payment" class="mt-0.5 size-4 accent-brand-600">
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">{{ $label }}</p>
                                    <p class="mt-0.5 text-xs text-slate-500">{{ $value === 'cod' ? 'Pay in cash when your order arrives.' : 'Transfer to our bank account. Details are shown after you place the order.' }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </section>
            </div>

            <div class="lg:col-span-2">
                <div class="card bg-slate-50 p-6 lg:sticky lg:top-28">
                    <h2 class="text-lg font-bold text-slate-900">Your order</h2>
                    <ul class="mt-5 max-h-72 space-y-4 overflow-y-auto pr-1">
                        @foreach ($cart->lines() as $line)
                            <li class="flex items-center gap-3">
                                <div class="relative size-16 shrink-0 rounded-xl border border-slate-200 bg-white">
                                    <x-product-image :src="$line->product->image_url" :alt="$line->product->name" class="size-full p-1.5" />
                                    <span class="absolute -top-2 -right-2 flex size-5 items-center justify-center rounded-full bg-slate-700 text-[11px] font-bold text-white">{{ $line->quantity }}</span>
                                </div>
                                <p class="line-clamp-2 flex-1 text-sm font-medium text-slate-700">{{ $line->product->name }}</p>
                                <p class="text-sm font-semibold whitespace-nowrap text-slate-900">{{ money($line->total) }}</p>
                            </li>
                        @endforeach
                    </ul>
                    <div class="mt-6 border-t border-slate-200 pt-6">@include('shop.partials.summary')</div>
                    <button class="btn-primary mt-6 w-full !py-3.5" :disabled="submitting">
                        <span x-text="submitting ? 'Placing order…' : 'Place order · {{ money($cart->total()) }}'">Place order</span>
                    </button>
                    <p class="mt-4 text-center text-xs text-slate-500">By placing your order you agree to our terms and return policy.</p>
                </div>
            </div>
        </form>
    </div>
</x-layouts.shop>
