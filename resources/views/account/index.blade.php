<x-layouts.shop title="My account">
    <div class="container-shop py-12">
        <h1 class="text-3xl font-extrabold tracking-tight text-slate-900">My account</h1>

        <div class="mt-8 flex flex-col gap-8 lg:flex-row">
            @include('account.partials.nav')

            <div class="min-w-0 flex-1 space-y-8">
                <section class="card overflow-hidden">
                    <div class="border-b border-slate-200 px-6 py-4">
                        <h2 class="font-bold text-slate-900">Order history</h2>
                    </div>
                    @if ($orders->isEmpty())
                        <div class="px-6 py-14 text-center">
                            <p class="text-slate-500">You haven't placed any orders yet.</p>
                            <a href="{{ route('shop') }}" class="btn-primary mt-5">Start shopping</a>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead class="bg-slate-50 text-xs font-semibold tracking-wide text-slate-500 uppercase">
                                    <tr><th class="px-6 py-3">Order</th><th class="px-6 py-3">Date</th><th class="px-6 py-3">Items</th><th class="px-6 py-3">Total</th><th class="px-6 py-3">Status</th><th class="px-6 py-3"></th></tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach ($orders as $order)
                                        <tr class="hover:bg-slate-50/60">
                                            <td class="px-6 py-4 font-mono font-semibold whitespace-nowrap text-slate-900">{{ $order->order_number }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-slate-500">{{ $order->created_at->format('d M Y') }}</td>
                                            <td class="px-6 py-4 text-slate-500">{{ $order->items_count }}</td>
                                            <td class="px-6 py-4 font-semibold whitespace-nowrap text-slate-900">{{ money($order->total) }}</td>
                                            <td class="px-6 py-4">@include('account.partials.status')</td>
                                            <td class="px-6 py-4 text-right"><a href="{{ route('account.order', $order) }}" class="font-semibold whitespace-nowrap text-brand-600 hover:text-brand-700">View →</a></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if ($orders->hasPages())<div class="border-t border-slate-200 px-6 py-4">{{ $orders->links() }}</div>@endif
                    @endif
                </section>

                <section class="card p-6">
                    <h2 class="font-bold text-slate-900">Profile & default address</h2>
                    <form action="{{ route('account.profile') }}" method="POST" class="mt-5 grid gap-4 sm:grid-cols-2">
                        @csrf @method('PUT')
                        <div>
                            <label class="label" for="name">Full name</label>
                            <input id="name" name="name" value="{{ old('name', $user->name) }}" required class="input">
                            @error('name')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="label" for="phone">Phone</label>
                            <input id="phone" name="phone" value="{{ old('phone', $user->phone) }}" class="input">
                        </div>
                        <div>
                            <label class="label" for="address">Address</label>
                            <input id="address" name="address" value="{{ old('address', $user->address) }}" class="input">
                        </div>
                        <div>
                            <label class="label" for="city">City</label>
                            <input id="city" name="city" value="{{ old('city', $user->city) }}" class="input">
                        </div>
                        <div class="sm:col-span-2"><button class="btn-primary">Save changes</button></div>
                    </form>
                </section>
            </div>
        </div>
    </div>
</x-layouts.shop>
