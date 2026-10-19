<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    private function product(array $attributes = []): Product
    {
        $category = Category::create(['name' => 'Electronics', 'slug' => 'electronics']);

        return Product::create([
            'category_id' => $category->id,
            'name' => 'Test Phone',
            'slug' => 'test-phone',
            'price' => 2000,
            'stock' => 5,
            ...$attributes,
        ]);
    }

    public function test_store_pages_load(): void
    {
        $product = $this->product();

        $this->get('/')->assertOk()->assertSee('Test Phone');
        $this->get('/shop?q=phone')->assertOk()->assertSee('Test Phone');
        $this->get('/category/electronics')->assertOk();
        $this->get(route('products.show', $product))->assertOk()->assertSee('Test Phone');
    }

    public function test_guest_can_checkout_with_coupon(): void
    {
        $product = $this->product();
        Coupon::create(['code' => 'SAVE10', 'type' => 'percent', 'value' => 10]);

        $this->post(route('cart.add', $product), ['quantity' => 2])->assertRedirect();
        $this->post(route('cart.coupon.apply'), ['code' => 'save10'])->assertSessionHasNoErrors();

        $this->post(route('checkout.store'), [
            'customer_name' => 'Ali Khan',
            'customer_email' => 'ali@example.com',
            'customer_phone' => '03001234567',
            'address' => '1 Mall Road',
            'city' => 'Lahore',
            'payment_method' => 'cod',
        ])->assertRedirect();

        $order = Order::with('items')->sole();
        $this->assertEquals(4000, (float) $order->subtotal);
        $this->assertEquals(400, (float) $order->discount);
        $this->assertEquals(250, (float) $order->shipping); // below the Rs. 5,000 free-shipping threshold
        $this->assertEquals(3850, (float) $order->total);
        $this->assertSame(1, $order->items->count());
        $this->assertSame(3, $product->fresh()->stock);
        $this->assertSame(1, Coupon::first()->used_count);
    }

    public function test_cannot_buy_more_than_stock(): void
    {
        $product = $this->product(['stock' => 1]);

        $this->post(route('cart.add', $product), ['quantity' => 5]);

        $this->assertSame([$product->id => 1], session('cart.items'));
    }

    public function test_cancelling_an_order_restores_stock(): void
    {
        $product = $this->product();
        $order = Order::create([
            'subtotal' => 4000, 'total' => 4000, 'customer_name' => 'A', 'customer_email' => 'a@example.com',
            'customer_phone' => '1', 'address' => 'x', 'city' => 'y',
        ]);
        $order->items()->create(['product_id' => $product->id, 'product_name' => 'Test Phone', 'price' => 2000, 'quantity' => 2, 'total' => 4000]);

        $order->update(['status' => 'cancelled']);

        $this->assertSame(7, $product->fresh()->stock);
    }

    public function test_only_admins_can_open_the_admin_panel(): void
    {
        $this->actingAs(User::factory()->create())->get('/admin')->assertForbidden();

        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();
        $this->actingAs($admin)->get('/admin')->assertOk();
    }
}
