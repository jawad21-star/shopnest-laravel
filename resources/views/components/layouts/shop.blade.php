<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title.' — ' : '' }}{{ config('shop.name') }}</title>
    <meta name="description" content="{{ $description ?? config('shop.name').' — shop the latest electronics, fashion, beauty and home essentials with fast delivery.' }}">

    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col" x-data="{ mobileMenu: false, search: false }"
      x-init="$store.cart.count = {{ $cartCount }}">

    {{-- Announcement bar --}}
    <div class="bg-slate-900 text-white">
        <div class="container-shop flex h-9 items-center justify-center gap-2 text-xs font-medium sm:justify-between">
            <p class="flex items-center gap-2"><x-shop-icon name="truck" class="size-4" /> Free delivery on orders over {{ money(config('shop.free_shipping_over')) }}</p>
            <p class="hidden items-center gap-4 text-slate-300 sm:flex">
                <span class="flex items-center gap-1.5"><x-shop-icon name="phone" class="size-3.5" /> {{ config('shop.phone') }}</span>
                <span>Use code <strong class="text-white">WELCOME10</strong> for 10% off</span>
            </p>
        </div>
    </div>

    {{-- Header --}}
    <header class="sticky top-0 z-40 border-b border-slate-200/80 bg-white/85 backdrop-blur-lg">
        <div class="container-shop flex h-16 items-center gap-4 lg:h-[72px]">
            <button class="btn-ghost -ml-2 !p-2 lg:hidden" @click="mobileMenu = true" aria-label="Open menu">
                <x-shop-icon name="menu" class="size-6" />
            </button>

            <a href="{{ route('home') }}" class="flex items-center gap-2 text-xl font-extrabold tracking-tight text-slate-900">
                <span class="flex size-9 items-center justify-center rounded-xl bg-gradient-to-br from-brand-500 to-violet-600 text-white shadow-lg shadow-brand-500/30">
                    <x-shop-icon name="bag" class="size-5" />
                </span>
                {{ config('shop.name') }}
            </a>

            <nav class="ml-4 hidden items-center gap-0.5 whitespace-nowrap lg:flex">
                <a href="{{ route('shop') }}" @class(['rounded-full px-3 py-2 text-sm font-medium transition hover:bg-slate-100', 'text-brand-600' => request()->routeIs('shop')])>Shop all</a>
                @foreach ($navCategories->take(4) as $navCategory)
                    <a href="{{ route('category.show', $navCategory) }}" @class(['rounded-full px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-900', 'hidden xl:inline-flex' => $loop->index >= 2, '!text-brand-600' => request()->route('category')?->is($navCategory)])>{{ $navCategory->name }}</a>
                @endforeach
            </nav>

            <form action="{{ route('shop') }}" class="relative ml-auto hidden w-full max-w-xs md:block">
                <x-shop-icon name="search" class="pointer-events-none absolute top-1/2 left-3.5 size-4 -translate-y-1/2 text-slate-400" />
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Search products…"
                       class="w-full rounded-full border border-slate-200 bg-slate-50 py-2 pr-4 pl-10 text-sm transition focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-100 focus:outline-none">
            </form>

            <div class="ml-auto flex items-center gap-1 md:ml-2">
                <button class="btn-ghost !p-2.5 md:hidden" @click="search = !search" aria-label="Search"><x-shop-icon name="search" /></button>
                @auth
                    <a href="{{ route('wishlist') }}" class="btn-ghost hidden !p-2.5 sm:inline-flex" aria-label="Wishlist"><x-shop-icon name="heart" /></a>
                @endauth
                <a href="{{ auth()->check() ? route('account') : route('login') }}" class="btn-ghost !p-2.5" aria-label="Account"><x-shop-icon name="user" /></a>
                <a href="{{ route('cart.index') }}" class="btn-ghost relative !p-2.5" aria-label="Cart">
                    <x-shop-icon name="bag" />
                    <span x-show="$store.cart.count > 0" x-cloak x-text="$store.cart.count"
                          class="absolute -top-0.5 -right-0.5 flex min-w-5 items-center justify-center rounded-full bg-brand-600 px-1 text-[11px] leading-5 font-bold text-white ring-2 ring-white"></span>
                </a>
            </div>
        </div>

        <div x-show="search" x-collapse x-cloak class="border-t border-slate-100 md:hidden">
            <form action="{{ route('shop') }}" class="container-shop py-3">
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Search products…" class="input">
            </form>
        </div>
    </header>

    {{-- Mobile menu --}}
    <div x-show="mobileMenu" x-cloak class="fixed inset-0 z-50 lg:hidden">
        <div x-show="mobileMenu" x-transition.opacity class="absolute inset-0 bg-slate-900/50" @click="mobileMenu = false"></div>
        <aside x-show="mobileMenu" x-transition:enter="transition duration-300" x-transition:enter-start="-translate-x-full"
               x-transition:leave="transition duration-200" x-transition:leave-end="-translate-x-full"
               class="absolute inset-y-0 left-0 flex w-80 max-w-[85%] flex-col bg-white shadow-2xl">
            <div class="flex h-16 items-center justify-between border-b border-slate-100 px-5">
                <span class="text-lg font-extrabold">{{ config('shop.name') }}</span>
                <button class="btn-ghost -mr-2 !p-2" @click="mobileMenu = false" aria-label="Close menu"><x-shop-icon name="x" class="size-6" /></button>
            </div>
            <nav class="flex-1 overflow-y-auto p-3">
                <a href="{{ route('shop') }}" class="flex items-center justify-between rounded-xl px-3 py-3 font-semibold hover:bg-slate-50">Shop all <x-shop-icon name="chevron-right" class="size-4 text-slate-400" /></a>
                @foreach ($navCategories as $navCategory)
                    <a href="{{ route('category.show', $navCategory) }}" class="flex items-center justify-between rounded-xl px-3 py-3 font-medium text-slate-700 hover:bg-slate-50">{{ $navCategory->name }} <x-shop-icon name="chevron-right" class="size-4 text-slate-400" /></a>
                @endforeach
                <div class="my-3 border-t border-slate-100"></div>
                @auth
                    <a href="{{ route('account') }}" class="block rounded-xl px-3 py-3 font-medium text-slate-700 hover:bg-slate-50">My account</a>
                    <a href="{{ route('wishlist') }}" class="block rounded-xl px-3 py-3 font-medium text-slate-700 hover:bg-slate-50">Wishlist</a>
                @else
                    <a href="{{ route('login') }}" class="block rounded-xl px-3 py-3 font-medium text-slate-700 hover:bg-slate-50">Sign in</a>
                    <a href="{{ route('register') }}" class="block rounded-xl px-3 py-3 font-medium text-slate-700 hover:bg-slate-50">Create account</a>
                @endauth
            </nav>
        </aside>
    </div>

    <main class="flex-1">
        {{ $slot }}
    </main>

    {{-- Footer --}}
    <footer class="mt-24 bg-slate-950 text-slate-400">
        <div class="container-shop grid gap-10 py-16 sm:grid-cols-2 lg:grid-cols-5">
            <div class="lg:col-span-2">
                <a href="{{ route('home') }}" class="flex items-center gap-2 text-xl font-extrabold text-white">
                    <span class="flex size-9 items-center justify-center rounded-xl bg-gradient-to-br from-brand-500 to-violet-600"><x-shop-icon name="bag" class="size-5" /></span>
                    {{ config('shop.name') }}
                </a>
                <p class="mt-4 max-w-sm text-sm leading-relaxed">Your one-stop shop for electronics, fashion, beauty and home essentials. Genuine products, fast delivery and easy returns.</p>
                <div class="mt-5 space-y-2 text-sm">
                    <p class="flex items-center gap-2"><x-shop-icon name="phone" class="size-4" /> {{ config('shop.phone') }}</p>
                    <p class="flex items-center gap-2"><x-shop-icon name="mail" class="size-4" /> {{ config('shop.email') }}</p>
                </div>
            </div>
            <div>
                <h4 class="text-sm font-semibold text-white">Shop</h4>
                <ul class="mt-4 space-y-2.5 text-sm">
                    @foreach ($navCategories->take(5) as $navCategory)
                        <li><a href="{{ route('category.show', $navCategory) }}" class="transition hover:text-white">{{ $navCategory->name }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div>
                <h4 class="text-sm font-semibold text-white">Account</h4>
                <ul class="mt-4 space-y-2.5 text-sm">
                    <li><a href="{{ route('account') }}" class="transition hover:text-white">My account</a></li>
                    <li><a href="{{ route('account') }}" class="transition hover:text-white">Order history</a></li>
                    <li><a href="{{ route('wishlist') }}" class="transition hover:text-white">Wishlist</a></li>
                    <li><a href="{{ route('cart.index') }}" class="transition hover:text-white">Cart</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-sm font-semibold text-white">Newsletter</h4>
                <p class="mt-4 text-sm">Get exclusive deals and new arrivals in your inbox.</p>
                <form class="mt-4 flex gap-2" x-data @submit.prevent="$el.reset(); $dispatch('toast', { message: 'Thanks for subscribing!' })">
                    <input type="email" required placeholder="you@email.com" class="w-full min-w-0 rounded-full border border-slate-700 bg-slate-900 px-4 py-2 text-sm text-white placeholder:text-slate-500 focus:border-brand-500 focus:outline-none">
                    <button class="btn-brand shrink-0 !px-4">Join</button>
                </form>
            </div>
        </div>
        <div class="border-t border-slate-800">
            <div class="container-shop flex flex-col items-center justify-between gap-2 py-6 text-xs sm:flex-row">
                <p>&copy; {{ date('Y') }} {{ config('shop.name') }}. All rights reserved.</p>
                <p>Cash on Delivery · Bank Transfer</p>
            </div>
        </div>
    </footer>

    {{-- Toasts --}}
    <div x-data="toasts" @toast.window="add($event.detail)"
         x-init="@if (session('success')) add({ message: @js(session('success')) }); @endif
                 @if (session('error')) add({ message: @js(session('error')), type: 'error' }); @endif"
         class="pointer-events-none fixed inset-x-0 bottom-4 z-[60] flex flex-col items-center gap-2 px-4 sm:inset-x-auto sm:right-6 sm:bottom-6 sm:items-end">
        <template x-for="toast in items" :key="toast.id">
            <div x-transition:enter="transition duration-300" x-transition:enter-start="translate-y-4 opacity-0"
                 class="pointer-events-auto flex w-full max-w-sm items-center gap-3 rounded-2xl px-4 py-3 text-sm font-medium text-white shadow-2xl"
                 :class="toast.type === 'error' ? 'bg-rose-600' : 'bg-slate-900'">
                <svg class="size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" :d="toast.type === 'error' ? 'M12 9v3.75m0 3.75h.008M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z' : 'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'"/></svg>
                <span x-text="toast.message" class="flex-1"></span>
                <a href="{{ route('cart.index') }}" x-show="toast.message.includes('added to cart')" class="font-semibold text-brand-200 underline-offset-2 hover:underline">View cart</a>
            </div>
        </template>
    </div>
</body>
</html>
