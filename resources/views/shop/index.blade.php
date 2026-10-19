@php
    $heading = $category?->name ?? (request('q') ? 'Results for “'.request('q').'”' : (request()->boolean('sale') ? 'On sale' : 'All products'));
    $action = $category ? route('category.show', $category) : route('shop');
    $selectedBrands = (array) request('brands', []);
@endphp

<x-layouts.shop :title="$heading">
    <div class="border-b border-slate-100 bg-slate-50">
        <div class="container-shop py-10">
            <nav class="flex items-center gap-1.5 text-sm text-slate-500">
                <a href="{{ route('home') }}" class="hover:text-slate-900">Home</a>
                <x-shop-icon name="chevron-right" class="size-3.5" />
                <a href="{{ route('shop') }}" class="hover:text-slate-900">Shop</a>
                @if ($category)
                    <x-shop-icon name="chevron-right" class="size-3.5" />
                    <span class="text-slate-900">{{ $category->name }}</span>
                @endif
            </nav>
            <h1 class="mt-3 text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">{{ $heading }}</h1>
            @if ($category?->description)
                <p class="mt-2 max-w-2xl text-slate-500">{{ $category->description }}</p>
            @endif
        </div>
    </div>

    <div class="container-shop py-10" x-data="{ filters: false }">
        <div class="flex gap-10">
            {{-- Filters --}}
            <aside class="fixed inset-0 z-50 max-lg:hidden lg:static lg:z-auto lg:w-64 lg:shrink-0" :class="filters && '!block'">
                <div class="absolute inset-0 bg-slate-900/50 lg:hidden" @click="filters = false"></div>
                <form action="{{ $action }}" method="GET"
                      class="absolute inset-y-0 right-0 w-80 max-w-[85%] overflow-y-auto bg-white p-6 lg:static lg:w-auto lg:p-0">
                    <div class="mb-6 flex items-center justify-between lg:hidden">
                        <h2 class="text-lg font-bold">Filters</h2>
                        <button type="button" class="btn-ghost !p-2" @click="filters = false"><x-shop-icon name="x" /></button>
                    </div>

                    @if (request('q'))<input type="hidden" name="q" value="{{ request('q') }}">@endif
                    @if (request('sort'))<input type="hidden" name="sort" value="{{ request('sort') }}">@endif

                    <div class="space-y-8">
                        @unless ($category)
                            <div>
                                <h3 class="text-sm font-semibold text-slate-900">Categories</h3>
                                <ul class="mt-3 space-y-1">
                                    @foreach ($categories as $cat)
                                        <li>
                                            <a href="{{ route('category.show', $cat) }}" class="flex items-center justify-between rounded-lg px-2 py-1.5 text-sm text-slate-600 transition hover:bg-slate-50 hover:text-slate-900">
                                                {{ $cat->name }} <span class="text-xs text-slate-400">{{ $cat->products_count }}</span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endunless

                        <div>
                            <h3 class="text-sm font-semibold text-slate-900">Price ({{ config('shop.currency') }})</h3>
                            <div class="mt-3 flex items-center gap-2">
                                <input type="number" name="min" value="{{ request('min') }}" placeholder="Min" min="0" class="input !px-3 !py-2">
                                <span class="text-slate-400">–</span>
                                <input type="number" name="max" value="{{ request('max') }}" placeholder="Max" min="0" class="input !px-3 !py-2">
                            </div>
                        </div>

                        <div class="space-y-3">
                            <h3 class="text-sm font-semibold text-slate-900">Availability</h3>
                            <label class="flex items-center gap-2.5 text-sm text-slate-600">
                                <input type="checkbox" name="in_stock" value="1" @checked(request()->boolean('in_stock')) class="size-4 rounded border-slate-300 text-brand-600 accent-brand-600"> In stock only
                            </label>
                            <label class="flex items-center gap-2.5 text-sm text-slate-600">
                                <input type="checkbox" name="sale" value="1" @checked(request()->boolean('sale')) class="size-4 rounded border-slate-300 accent-brand-600"> On sale
                            </label>
                        </div>

                        @if ($brands->isNotEmpty())
                            <div x-data="{ all: false }">
                                <h3 class="text-sm font-semibold text-slate-900">Brands</h3>
                                <div class="mt-3 space-y-2.5">
                                    @foreach ($brands as $i => $brand)
                                        <label class="flex items-center gap-2.5 text-sm text-slate-600" @if ($i >= 8 && ! in_array($brand->slug, $selectedBrands)) x-show="all" x-cloak @endif>
                                            <input type="checkbox" name="brands[]" value="{{ $brand->slug }}" @checked(in_array($brand->slug, $selectedBrands)) class="size-4 rounded accent-brand-600"> {{ $brand->name }}
                                        </label>
                                    @endforeach
                                </div>
                                @if ($brands->count() > 8)
                                    <button type="button" @click="all = !all" class="mt-3 text-sm font-semibold text-brand-600" x-text="all ? 'Show less' : 'Show all {{ $brands->count() }} brands'"></button>
                                @endif
                            </div>
                        @endif

                        <div class="flex gap-2">
                            <button class="btn-primary flex-1">Apply filters</button>
                            <a href="{{ $action }}" class="btn-outline">Reset</a>
                        </div>
                    </div>
                </form>
            </aside>

            {{-- Results --}}
            <div class="min-w-0 flex-1">
                <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
                    <p class="text-sm text-slate-500">
                        Showing <span class="font-semibold text-slate-900">{{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }}</span> of {{ $products->total() }} products
                    </p>
                    <div class="flex items-center gap-2">
                        <button type="button" class="btn-outline !px-4 !py-2 lg:hidden" @click="filters = true"><x-shop-icon name="filter" class="size-4" /> Filters</button>
                        <form action="{{ $action }}" method="GET">
                            @foreach (request()->except(['sort', 'page']) as $key => $value)
                                @foreach ((array) $value as $v)
                                    <input type="hidden" name="{{ is_array($value) ? $key.'[]' : $key }}" value="{{ $v }}">
                                @endforeach
                            @endforeach
                            <select name="sort" onchange="this.form.submit()" class="rounded-full border border-slate-300 bg-white py-2 pr-9 pl-4 text-sm font-medium focus:border-brand-500 focus:outline-none">
                                @foreach (['newest' => 'Newest', 'popular' => 'Best selling', 'price_asc' => 'Price: low to high', 'price_desc' => 'Price: high to low', 'name' => 'Name A–Z'] as $value => $label)
                                    <option value="{{ $value }}" @selected(request('sort', 'newest') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                </div>

                @if ($products->isEmpty())
                    <div class="card flex flex-col items-center px-6 py-20 text-center">
                        <span class="flex size-16 items-center justify-center rounded-full bg-slate-100 text-slate-400"><x-shop-icon name="search" class="size-7" /></span>
                        <h2 class="mt-5 text-lg font-semibold text-slate-900">No products found</h2>
                        <p class="mt-1 text-sm text-slate-500">Try adjusting your filters or search terms.</p>
                        <a href="{{ route('shop') }}" class="btn-primary mt-6">Browse all products</a>
                    </div>
                @else
                    <div class="grid grid-cols-2 gap-4 sm:gap-6 xl:grid-cols-3">
                        @foreach ($products as $product)
                            <x-product-card :product="$product" />
                        @endforeach
                    </div>
                    <div class="mt-10">{{ $products->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.shop>
