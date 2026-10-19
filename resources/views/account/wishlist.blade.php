<x-layouts.shop title="Wishlist">
    <div class="container-shop py-12">
        <h1 class="text-3xl font-extrabold tracking-tight text-slate-900">My wishlist</h1>

        <div class="mt-8 flex flex-col gap-8 lg:flex-row">
            @include('account.partials.nav')

            <div class="min-w-0 flex-1">
                @if ($products->isEmpty())
                    <div class="card flex flex-col items-center px-6 py-16 text-center">
                        <span class="flex size-16 items-center justify-center rounded-full bg-rose-50 text-rose-400"><x-shop-icon name="heart" class="size-7" /></span>
                        <p class="mt-5 font-semibold text-slate-900">Your wishlist is empty</p>
                        <p class="mt-1 text-sm text-slate-500">Tap the heart on any product to save it here.</p>
                        <a href="{{ route('shop') }}" class="btn-primary mt-6">Discover products</a>
                    </div>
                @else
                    <div class="grid grid-cols-2 gap-4 sm:gap-6 xl:grid-cols-3">
                        @foreach ($products as $product)
                            <x-product-card :product="$product" />
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.shop>
