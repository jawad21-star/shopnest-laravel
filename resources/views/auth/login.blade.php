<x-layouts.shop title="Sign in">
    <div class="container-shop flex justify-center py-16">
        <div class="w-full max-w-md">
            <div class="text-center">
                <h1 class="text-3xl font-extrabold tracking-tight text-slate-900">Welcome back</h1>
                <p class="mt-2 text-slate-500">Sign in to track orders and checkout faster.</p>
            </div>
            <form action="{{ route('login') }}" method="POST" class="card mt-8 space-y-5 p-8 shadow-xl shadow-slate-200/50">
                @csrf
                <div>
                    <label class="label" for="email">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email" class="input">
                    @error('email')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="label" for="password">Password</label>
                    <input id="password" type="password" name="password" required autocomplete="current-password" class="input">
                </div>
                <label class="flex items-center gap-2 text-sm text-slate-600">
                    <input type="checkbox" name="remember" value="1" class="size-4 accent-brand-600"> Remember me
                </label>
                <button class="btn-primary w-full !py-3">Sign in</button>
                <p class="text-center text-sm text-slate-500">New here? <a href="{{ route('register') }}" class="font-semibold text-brand-600 hover:underline">Create an account</a></p>
            </form>
        </div>
    </div>
</x-layouts.shop>
