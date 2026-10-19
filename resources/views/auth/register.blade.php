<x-layouts.shop title="Create account">
    <div class="container-shop flex justify-center py-16">
        <div class="w-full max-w-md">
            <div class="text-center">
                <h1 class="text-3xl font-extrabold tracking-tight text-slate-900">Create your account</h1>
                <p class="mt-2 text-slate-500">Join {{ config('shop.name') }} for exclusive deals.</p>
            </div>
            <form action="{{ route('register') }}" method="POST" class="card mt-8 space-y-5 p-8 shadow-xl shadow-slate-200/50">
                @csrf
                <div>
                    <label class="label" for="name">Full name</label>
                    <input id="name" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" class="input">
                    @error('name')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="label" for="email">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" class="input">
                    @error('email')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="label" for="phone">Phone <span class="font-normal text-slate-400">(optional)</span></label>
                    <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" autocomplete="tel" class="input">
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="label" for="password">Password</label>
                        <input id="password" type="password" name="password" required autocomplete="new-password" class="input">
                    </div>
                    <div>
                        <label class="label" for="password_confirmation">Confirm</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="input">
                    </div>
                    @error('password')<p class="-mt-2 text-xs text-rose-600 sm:col-span-2">{{ $message }}</p>@enderror
                </div>
                <button class="btn-primary w-full !py-3">Create account</button>
                <p class="text-center text-sm text-slate-500">Already registered? <a href="{{ route('login') }}" class="font-semibold text-brand-600 hover:underline">Sign in</a></p>
            </form>
        </div>
    </div>
</x-layouts.shop>
