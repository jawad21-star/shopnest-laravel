<x-layouts.shop>
    {{-- Hero --}}
    <section class="relative overflow-hidden bg-gradient-to-br from-slate-950 via-brand-900 to-violet-900 text-white">
        <div class="pointer-events-none absolute -top-40 -right-40 size-[520px] rounded-full bg-brand-500/30 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-40 -left-20 size-[420px] rounded-full bg-fuchsia-500/20 blur-3xl"></div>

        <div class="container-shop relative grid items-center gap-12 py-16 lg:grid-cols-2 lg:py-24">
            <div class="animate-fade-up">
                <span class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3.5 py-1.5 text-xs font-semibold backdrop-blur">
                    <x-shop-icon name="sparkles" class="size-4 text-amber-300" /> New season collection is live
                </span>
                <h1 class="mt-6 text-4xl leading-[1.05] font-extrabold tracking-tight sm:text-5xl lg:text-6xl">
                    Everything you love,<br>
                    <span class="bg-gradient-to-r from-amber-200 via-pink-200 to-brand-200 bg-clip-text text-transparent">delivered to your door.</span>
                </h1>
                <p class="mt-6 max-w-lg text-lg text-slate-300">Discover top brands in electronics, fashion, beauty and home — with cash on delivery and easy 7-day returns.</p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('shop') }}" class="btn bg-white !px-7 !py-3 text-slate-900 hover:bg-brand-50">Shop now <x-shop-icon name="arrow-right" class="size-4" /></a>
                    <a href="{{ route('shop', ['sale' => 1]) }}" class="btn border border-white/25 !px-7 !py-3 text-white hover:bg-white/10">View deals</a>
                </div>
                <dl class="mt-12 grid max-w-md grid-cols-3 gap-6 border-t border-white/10 pt-8">
                    <div><dt class="text-xs text-slate-400">Products</dt><dd class="mt-1 text-2xl font-bold">{{ \App\Models\Product::active()->count() }}+</dd></div>
                    <div><dt class="text-xs text-slate-400">Brands</dt><dd class="mt-1 text-2xl font-bold">{{ $brands->count() }}+</dd></div>
                    <div><dt class="text-xs text-slate-400">Delivery</dt><dd class="mt-1 text-2xl font-bold whitespace-nowrap">2-4 days</dd></div>
                </dl>
            </div>

            @if ($featured->isNotEmpty())
                <div class="relative hidden lg:block">
                    <div class="grid grid-cols-2 gap-4">
                        @foreach ($featured->take(4) as $i => $item)
                            <a href="{{ route('products.show', $item) }}"
                               class="group animate-fade-up rounded-3xl border border-white/10 bg-white/[0.07] p-4 backdrop-blur transition hover:bg-white/[0.12] {{ $i % 2 ? 'mt-8 -mb-8' : '' }}"
                               style="animation-delay: {{ 150 + $i * 100 }}ms">
                                <div class="aspect-square overflow-hidden rounded-2xl bg-white">
                                    <x-product-image :src="$item->image_url" :alt="$item->name" class="size-full p-5 transition duration-500 group-hover:scale-105" />
                                </div>
                                <p class="mt-3 truncate text-sm font-medium">{{ $item->name }}</p>
                                <p class="text-sm font-bold text-amber-200">{{ money($item->price) }}</p>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>

    {{-- Perks --}}
    <section class="border-b border-slate-100">
        <div class="container-shop grid grid-cols-2 gap-6 py-8 lg:grid-cols-4">
            @foreach ([
                ['truck', 'Free delivery', 'On orders over '.money(config('shop.free_shipping_over'))],
                ['shield', 'Secure payments', 'COD & bank transfer'],
                ['refresh', 'Easy returns', '7-day return policy'],
                ['chat', '24/7 support', 'We are here to help'],
            ] as [$icon, $heading, $text])
                <div class="flex items-center gap-3.5">
                    <span class="flex size-11 shrink-0 items-center justify-center rounded-2xl bg-brand-50 text-brand-600"><x-shop-icon :name="$icon" class="size-5" /></span>
                    <div>
                        <p class="text-sm font-semibold text-slate-900">{{ $heading }}</p>
                        <p class="text-xs text-slate-500">{{ $text }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Categories --}}
    @if ($categories->isNotEmpty())
        <section class="container-shop pt-16">
            <div class="flex items-end justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Shop by category</h2>
                    <p class="mt-1 text-slate-500">Find exactly what you're looking for.</p>
                </div>
                <a href="{{ route('shop') }}" class="hidden items-center gap-1 text-sm font-semibold text-brand-600 hover:text-brand-700 sm:flex">All products <x-shop-icon name="arrow-right" class="size-4" /></a>
            </div>
            <div class="mt-8 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
                @foreach ($categories as $category)
                    <a href="{{ route('category.show', $category) }}" class="group rounded-2xl border border-slate-200 bg-white p-4 text-center transition hover:-translate-y-1 hover:border-brand-200 hover:shadow-lg hover:shadow-brand-100/50">
                        <div class="mx-auto aspect-square w-full overflow-hidden rounded-xl bg-slate-50">
                            <x-product-image :src="$category->image_url" :alt="$category->name" class="size-full p-4 transition duration-500 group-hover:scale-110" />
                        </div>
                        <p class="mt-3 text-sm font-semibold text-slate-900">{{ $category->name }}</p>
                        <p class="text-xs text-slate-400">{{ $category->products_count }} items</p>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Featured --}}
    @if ($featured->isNotEmpty())
        <section class="container-shop pt-20">
            <div class="flex items-end justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Featured products</h2>
                    <p class="mt-1 text-slate-500">Hand-picked favourites from our collection.</p>
                </div>
                <a href="{{ route('shop', ['sort' => 'popular']) }}" class="hidden items-center gap-1 text-sm font-semibold text-brand-600 hover:text-brand-700 sm:flex">View all <x-shop-icon name="arrow-right" class="size-4" /></a>
            </div>
            <div class="mt-8 grid grid-cols-2 gap-4 sm:gap-6 lg:grid-cols-4">
                @foreach ($featured as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
        </section>
    @endif

    {{-- Promo banner --}}
    <section class="container-shop pt-20">
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-amber-400 via-orange-400 to-rose-500 px-8 py-12 sm:px-12 lg:py-16">
            <div class="pointer-events-none absolute -right-10 -bottom-24 size-72 rounded-full bg-white/20 blur-2xl"></div>
            <div class="relative max-w-xl">
                <p class="text-sm font-bold tracking-widest text-white/80 uppercase">Limited time offer</p>
                <h2 class="mt-3 text-3xl font-extrabold text-white sm:text-4xl">Get 25% off orders over {{ money(10000) }}</h2>
                <p class="mt-3 text-white/90">Use code <span class="rounded-lg bg-white/25 px-2 py-0.5 font-mono font-bold">MEGA25</span> at checkout. Valid while stocks last.</p>
                <a href="{{ route('shop', ['sale' => 1]) }}" class="btn mt-7 bg-slate-900 !px-7 !py-3 text-white hover:bg-slate-800">Shop the sale <x-shop-icon name="arrow-right" class="size-4" /></a>
            </div>
        </div>
    </section>

    {{-- New arrivals --}}
    @if ($newArrivals->isNotEmpty())
        <section class="container-shop pt-20">
            <div class="flex items-end justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">New arrivals</h2>
                    <p class="mt-1 text-slate-500">Fresh drops you don't want to miss.</p>
                </div>
                <a href="{{ route('shop') }}" class="hidden items-center gap-1 text-sm font-semibold text-brand-600 hover:text-brand-700 sm:flex">View all <x-shop-icon name="arrow-right" class="size-4" /></a>
            </div>
            <div class="mt-8 grid grid-cols-2 gap-4 sm:gap-6 lg:grid-cols-4">
                @foreach ($newArrivals as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
        </section>
    @endif

    {{-- Brands --}}
    @if ($brands->isNotEmpty())
        <section class="container-shop pt-20">
            <h2 class="text-center text-sm font-semibold tracking-widest text-slate-400 uppercase">Top brands we carry</h2>
            <div class="mt-6 flex flex-wrap justify-center gap-3">
                @foreach ($brands->take(24) as $brand)
                    <a href="{{ route('shop', ['brands' => [$brand->slug]]) }}" class="rounded-full border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 transition hover:border-slate-900 hover:text-slate-900">{{ $brand->name }}</a>
                @endforeach
            </div>
        </section>
    @endif
</x-layouts.shop>
