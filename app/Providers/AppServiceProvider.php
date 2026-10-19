<?php

namespace App\Providers;

use App\Models\Category;
use App\Services\Cart;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->scoped(Cart::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useTailwind();

        View::composer('components.layouts.shop', function ($view) {
            $view->with([
                'cartCount' => app(Cart::class)->count(),
                'navCategories' => Category::active()->whereNull('parent_id')->orderBy('sort_order')->take(6)->get(),
            ]);
        });
    }
}
