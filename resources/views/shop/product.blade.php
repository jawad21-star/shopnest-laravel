<x-layouts.shop :title="$product->name" :description="$product->short_description">
    <div class="container-shop py-8">
        <nav class="flex flex-wrap items-center gap-1.5 text-sm text-slate-500">
            <a href="{{ route('home') }}" class="hover:text-slate-900">Home</a>
            <x-shop-icon name="chevron-right" class="size-3.5" />
            @if ($product->category)
                <a href="{{ route('category.show', $product->category) }}" class="hover:text-slate-900">{{ $product->category->name }}</a>
                <x-shop-icon name="chevron-right" class="size-3.5" />
            @endif
            <span class="truncate text-slate-900">{{ $product->name }}</span>
        </nav>

        <div class="mt-8 grid gap-10 lg:grid-cols-2 lg:gap-16">
            {{-- Gallery --}}
            @php $images = $product->gallery_urls; @endphp
            <div x-data="{ active: 0, images: @js($images) }" class="lg:sticky lg:top-28 lg:self-start">
                <div class="relative aspect-square overflow-hidden rounded-3xl border border-slate-200 bg-slate-50">
                    @if (count($images))
                        <template x-for="(src, i) in images" :key="i">
                            <img :src="src" alt="{{ $product->name }}" x-show="active === i" x-transition.opacity.duration.300ms class="absolute inset-0 size-full object-contain p-10">
                        </template>
                    @else
                        <x-product-image :src="null" class="size-full" />
                    @endif
                    @if ($product->discount_percent)
                        <span class="absolute top-5 left-5 rounded-full bg-rose-500 px-3 py-1.5 text-sm font-bold text-white">-{{ $product->discount_percent }}%</span>
                    @endif
                </div>
                @if (count($images) > 1)
                    <div class="mt-4 grid grid-cols-5 gap-3">
                        @foreach ($images as $i => $src)
                            <button type="button" @click="active = {{ $i }}" class="aspect-square overflow-hidden rounded-xl border-2 bg-slate-50 transition"
                                    :class="active === {{ $i }} ? 'border-brand-600' : 'border-transparent hover:border-slate-300'">
                                <img src="{{ $src }}" alt="" loading="lazy" class="size-full object-contain p-2">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Info --}}
            <div>
                @if ($product->brand)
                    <a href="{{ route('shop', ['brands' => [$product->brand->slug]]) }}" class="text-sm font-semibold tracking-wide text-brand-600 uppercase hover:text-brand-700">{{ $product->brand->name }}</a>
                @endif
                <h1 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">{{ $product->name }}</h1>

                <div class="mt-3 flex items-center gap-3 text-sm">
                    @if ($product->approvedReviews->count())
                        <x-stars :rating="$rating" />
                        <a href="#reviews" class="text-slate-500 hover:text-slate-900">{{ number_format($rating, 1) }} · {{ $product->approvedReviews->count() }} {{ Str::plural('review', $product->approvedReviews->count()) }}</a>
                    @else
                        <span class="text-slate-400">No reviews yet</span>
                    @endif
                    @if ($product->sku)
                        <span class="text-slate-300">|</span><span class="text-slate-500">SKU: {{ $product->sku }}</span>
                    @endif
                </div>

                <div class="mt-6 flex items-baseline gap-3">
                    <p class="text-3xl font-extrabold text-slate-900">{{ money($product->price) }}</p>
                    @if ($product->discount_percent)
                        <p class="text-lg text-slate-400 line-through">{{ money($product->compare_price) }}</p>
                        <span class="rounded-full bg-rose-50 px-2.5 py-1 text-sm font-semibold text-rose-600">Save {{ money($product->compare_price - $product->price) }}</span>
                    @endif
                </div>

                @if ($product->short_description)
                    <p class="mt-5 leading-relaxed text-slate-600">{{ $product->short_description }}</p>
                @endif

                <div class="mt-6">
                    @if ($product->stock > 5)
                        <p class="inline-flex items-center gap-2 text-sm font-medium text-emerald-600"><span class="size-2 rounded-full bg-emerald-500"></span> In stock</p>
                    @elseif ($product->in_stock)
                        <p class="inline-flex items-center gap-2 text-sm font-medium text-amber-600"><span class="size-2 animate-pulse rounded-full bg-amber-500"></span> Only {{ $product->stock }} left — order soon</p>
                    @else
                        <p class="inline-flex items-center gap-2 text-sm font-medium text-rose-600"><span class="size-2 rounded-full bg-rose-500"></span> Out of stock</p>
                    @endif
                </div>

                @if ($product->in_stock)
                    <form action="{{ route('cart.add', $product) }}" method="POST" class="mt-6" x-data="{ qty: 1, max: {{ $product->stock }} }">
                        @csrf
                        <div class="flex flex-wrap items-center gap-3">
                            <div class="flex items-center rounded-full border border-slate-300">
                                <button type="button" @click="qty = Math.max(1, qty - 1)" class="flex size-12 items-center justify-center rounded-full text-slate-600 hover:text-slate-900" aria-label="Decrease"><x-shop-icon name="minus" class="size-4" /></button>
                                <input type="number" name="quantity" x-model.number="qty" min="1" :max="max" class="w-12 [appearance:textfield] border-0 bg-transparent text-center font-semibold focus:outline-none [&::-webkit-inner-spin-button]:appearance-none">
                                <button type="button" @click="qty = Math.min(max, qty + 1)" class="flex size-12 items-center justify-center rounded-full text-slate-600 hover:text-slate-900" aria-label="Increase"><x-shop-icon name="plus" class="size-4" /></button>
                            </div>
                            <div x-data="addToCart('{{ route('cart.add', $product) }}')" class="flex-1">
                                <button type="button" @click="add(qty)" :disabled="loading" class="btn-primary w-full !py-3.5">
                                    <x-shop-icon name="bag" class="size-5" /> <span x-text="loading ? 'Adding…' : 'Add to cart'">Add to cart</span>
                                </button>
                            </div>
                            @auth
                                <button type="button" x-data="wishlist('{{ route('wishlist.toggle', $product) }}', {{ $inWishlist ? 'true' : 'false' }})" @click="toggle()"
                                        class="flex size-12 items-center justify-center rounded-full border border-slate-300 transition hover:border-rose-300"
                                        :class="active ? 'text-rose-500 border-rose-300 bg-rose-50' : 'text-slate-600'" aria-label="Wishlist">
                                    <svg class="size-5" viewBox="0 0 24 24" :fill="active ? 'currentColor' : 'none'" stroke="currentColor" stroke-width="1.6"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z"/></svg>
                                </button>
                            @endauth
                        </div>
                        <button name="buy_now" value="1" class="btn-brand mt-3 w-full !py-3.5">Buy it now</button>
                    </form>
                @endif

                <div class="mt-8 grid grid-cols-1 gap-3 rounded-2xl bg-slate-50 p-5 text-sm sm:grid-cols-3">
                    <p class="flex items-center gap-2 text-slate-600"><x-shop-icon name="truck" class="size-5 text-brand-600" /> 2-4 day delivery</p>
                    <p class="flex items-center gap-2 text-slate-600"><x-shop-icon name="refresh" class="size-5 text-brand-600" /> 7-day returns</p>
                    <p class="flex items-center gap-2 text-slate-600"><x-shop-icon name="shield" class="size-5 text-brand-600" /> 100% genuine</p>
                </div>

                {{-- Tabs --}}
                <div class="mt-10" x-data="{ tab: 'description' }">
                    <div class="flex gap-6 border-b border-slate-200">
                        @foreach (['description' => 'Description', 'shipping' => 'Shipping & returns'] as $key => $label)
                            <button type="button" @click="tab = '{{ $key }}'" class="-mb-px border-b-2 pb-3 text-sm font-semibold transition"
                                    :class="tab === '{{ $key }}' ? 'border-slate-900 text-slate-900' : 'border-transparent text-slate-500 hover:text-slate-900'">{{ $label }}</button>
                        @endforeach
                    </div>
                    <div class="prose-shop pt-5 text-sm" x-show="tab === 'description'">{!! $product->description ?: '<p>No description available.</p>' !!}</div>
                    <div class="prose-shop pt-5 text-sm" x-show="tab === 'shipping'" x-cloak>
                        <p>Orders are dispatched within 24 hours and delivered in 2-4 working days across Pakistan.</p>
                        <ul>
                            <li>Free delivery on orders over {{ money(config('shop.free_shipping_over')) }}, otherwise {{ money(config('shop.shipping_fee')) }}.</li>
                            <li>Cash on delivery available nationwide.</li>
                            <li>Easy 7-day returns on unused items in original packaging.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- Reviews --}}
        <section id="reviews" class="mt-20 scroll-mt-28 border-t border-slate-200 pt-12">
            <div class="grid gap-10 lg:grid-cols-3">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900">Customer reviews</h2>
                    <div class="mt-4 flex items-center gap-3">
                        <p class="text-5xl font-extrabold text-slate-900">{{ number_format($rating, 1) }}</p>
                        <div>
                            <x-stars :rating="$rating" size="size-5" />
                            <p class="mt-1 text-sm text-slate-500">Based on {{ $product->approvedReviews->count() }} reviews</p>
                        </div>
                    </div>

                    @auth
                        @if ($canReview)
                            <form action="{{ route('reviews.store', $product) }}" method="POST" class="card mt-8 space-y-4 p-5" x-data="{ rating: 5, hover: 0 }">
                                @csrf
                                <p class="font-semibold text-slate-900">Write a review</p>
                                <div class="flex gap-1">
                                    <input type="hidden" name="rating" :value="rating">
                                    <template x-for="i in 5">
                                        <button type="button" @click="rating = i" @mouseenter="hover = i" @mouseleave="hover = 0" class="text-slate-200 transition" :class="(hover || rating) >= i && '!text-amber-400'">
                                            <svg class="size-7" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        </button>
                                    </template>
                                </div>
                                <textarea name="comment" rows="3" class="input" placeholder="Share your experience…">{{ old('comment') }}</textarea>
                                <button class="btn-primary w-full">Submit review</button>
                            </form>
                        @endif
                    @else
                        <p class="mt-8 text-sm text-slate-500"><a href="{{ route('login') }}" class="font-semibold text-brand-600 hover:underline">Sign in</a> to write a review.</p>
                    @endauth
                </div>

                <div class="space-y-4 lg:col-span-2">
                    @forelse ($product->approvedReviews as $review)
                        <div class="card p-5">
                            <div class="flex items-center justify-between gap-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex size-10 items-center justify-center rounded-full bg-brand-100 font-semibold text-brand-700">{{ Str::upper(Str::substr($review->user->name, 0, 1)) }}</span>
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900">{{ $review->user->name }}</p>
                                        <p class="text-xs text-slate-400">{{ $review->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <x-stars :rating="$review->rating" />
                            </div>
                            @if ($review->comment)
                                <p class="mt-3 text-sm leading-relaxed text-slate-600">{{ $review->comment }}</p>
                            @endif
                        </div>
                    @empty
                        <div class="card px-6 py-12 text-center text-sm text-slate-500">No reviews yet. Be the first to review this product!</div>
                    @endforelse
                </div>
            </div>
        </section>

        {{-- Related --}}
        @if ($related->isNotEmpty())
            <section class="mt-20">
                <h2 class="text-2xl font-bold text-slate-900">You may also like</h2>
                <div class="mt-8 grid grid-cols-2 gap-4 sm:gap-6 lg:grid-cols-4">
                    @foreach ($related as $item)
                        <x-product-card :product="$item" />
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</x-layouts.shop>
