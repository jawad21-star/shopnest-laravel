<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/shop', [ShopController::class, 'index'])->name('shop');
Route::get('/category/{category}', [ShopController::class, 'category'])->name('category.show');
Route::get('/product/{product}', [ShopController::class, 'show'])->name('products.show');

Route::prefix('cart')->name('cart.')->controller(CartController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/add/{product}', 'add')->name('add');
    Route::patch('/{productId}', 'update')->name('update');
    Route::delete('/{productId}', 'remove')->name('remove');
    Route::post('/coupon', 'applyCoupon')->name('coupon.apply');
    Route::delete('/coupon/remove', 'removeCoupon')->name('coupon.remove');
});

Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store')->middleware('throttle:10,1');
Route::get('/order/{order}/thank-you', [CheckoutController::class, 'success'])->name('checkout.success');

Route::middleware('guest')->controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLogin')->name('login');
    Route::post('/login', 'login')->middleware('throttle:6,1');
    Route::get('/register', 'showRegister')->name('register');
    Route::post('/register', 'register')->middleware('throttle:6,1');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/account', [AccountController::class, 'index'])->name('account');
    Route::get('/account/orders/{order}', [AccountController::class, 'order'])->name('account.order');
    Route::put('/account/profile', [AccountController::class, 'updateProfile'])->name('account.profile');

    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist');
    Route::post('/wishlist/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

    Route::post('/product/{product}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
});
