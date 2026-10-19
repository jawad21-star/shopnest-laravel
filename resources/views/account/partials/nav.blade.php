<aside class="lg:w-60 lg:shrink-0">
    <div class="card p-5">
        <div class="flex items-center gap-3">
            <span class="flex size-11 items-center justify-center rounded-full bg-gradient-to-br from-brand-500 to-violet-600 font-bold text-white">{{ Str::upper(Str::substr(auth()->user()->name, 0, 1)) }}</span>
            <div class="min-w-0">
                <p class="truncate font-semibold text-slate-900">{{ auth()->user()->name }}</p>
                <p class="truncate text-xs text-slate-500">{{ auth()->user()->email }}</p>
            </div>
        </div>
        <nav class="mt-5 space-y-1 text-sm">
            <a href="{{ route('account') }}" @class(['flex items-center gap-2.5 rounded-xl px-3 py-2.5 font-medium transition', 'bg-slate-900 text-white' => request()->routeIs('account*'), 'text-slate-600 hover:bg-slate-50' => ! request()->routeIs('account*')])><x-shop-icon name="box" class="size-4" /> Orders & profile</a>
            <a href="{{ route('wishlist') }}" @class(['flex items-center gap-2.5 rounded-xl px-3 py-2.5 font-medium transition', 'bg-slate-900 text-white' => request()->routeIs('wishlist'), 'text-slate-600 hover:bg-slate-50' => ! request()->routeIs('wishlist')])><x-shop-icon name="heart" class="size-4" /> Wishlist</a>
            @if (auth()->user()->is_admin)
                <a href="{{ url('/admin') }}" class="flex items-center gap-2.5 rounded-xl px-3 py-2.5 font-medium text-brand-600 hover:bg-brand-50"><x-shop-icon name="sparkles" class="size-4" /> Admin panel</a>
            @endif
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="flex w-full items-center gap-2.5 rounded-xl px-3 py-2.5 font-medium text-slate-600 transition hover:bg-rose-50 hover:text-rose-600"><x-shop-icon name="logout" class="size-4" /> Sign out</button>
            </form>
        </nav>
    </div>
</aside>
