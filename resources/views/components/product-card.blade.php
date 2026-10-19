@props(['product'])

<article class="group relative flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white transition duration-300 hover:-translate-y-1 hover:border-slate-300 hover:shadow-xl hover:shadow-slate-200/60">
    <a href="{{ route('products.show', $product) }}" class="relative block aspect-square overflow-hidden bg-slate-50">
        <x-product-image :src="$product->image_url" :alt="$product->name"
                         class="size-full p-6 transition duration-500 group-hover:scale-105" />

        <div class="absolute top-3 left-3 flex flex-col gap-1.5">
            @if ($product->discount_percent)
                <span class="rounded-full bg-rose-500 px-2.5 py-1 text-xs font-bold text-white">-{{ $product->discount_percent }}%</span>
            @endif
            @unless ($product->in_stock)
                <span class="rounded-full bg-slate-900/80 px-2.5 py-1 text-xs font-semibold text-white">Sold out</span>
            @endunless
        </div>
    </a>

    @auth
        <button type="button" x-data="wishlist('{{ route('wishlist.toggle', $product) }}', {{ auth()->user()->wishlistIds()->contains($product->id) ? 'true' : 'false' }})" @click="toggle()"
                class="absolute top-3 right-3 flex size-9 items-center justify-center rounded-full bg-white/90 text-slate-500 shadow-sm backdrop-blur transition hover:text-rose-500"
                :class="active && 'text-rose-500'" aria-label="Toggle wishlist">
            <svg class="size-5" viewBox="0 0 24 24" :fill="active ? 'currentColor' : 'none'" stroke="currentColor" stroke-width="1.6"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z"/></svg>
        </button>
    @endauth

    <div class="flex flex-1 flex-col p-4">
        @if ($product->category)
            <p class="text-xs font-medium tracking-wide text-slate-400 uppercase">{{ $product->category->name }}</p>
        @endif
        <h3 class="mt-1 line-clamp-2 text-sm font-semibold text-slate-900">
            <a href="{{ route('products.show', $product) }}" class="hover:text-brand-600">{{ $product->name }}</a>
        </h3>

        @if ($product->rating ?? false)
            <div class="mt-1.5 flex items-center gap-1.5">
                <x-stars :rating="$product->rating" size="size-3.5" />
                <span class="text-xs text-slate-400">{{ number_format($product->rating, 1) }}</span>
            </div>
        @endif

        <div class="mt-auto flex items-end justify-between gap-2 pt-3">
            <div>
                <p class="text-base font-bold text-slate-900">{{ money($product->price) }}</p>
                @if ($product->discount_percent)
                    <p class="text-xs text-slate-400 line-through">{{ money($product->compare_price) }}</p>
                @endif
            </div>

            @if ($product->in_stock)
                <button type="button" x-data="addToCart('{{ route('cart.add', $product) }}')" @click="add()" :disabled="loading"
                        class="flex size-10 items-center justify-center rounded-full bg-slate-900 text-white transition hover:bg-brand-600 disabled:opacity-60"
                        aria-label="Add {{ $product->name }} to cart">
                    <x-shop-icon name="plus" class="size-5" x-show="!loading" />
                    <svg x-show="loading" x-cloak class="size-5 animate-spin" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" class="opacity-25"/><path d="M4 12a8 8 0 018-8" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>
                </button>
            @endif
        </div>
    </div>
</article>
