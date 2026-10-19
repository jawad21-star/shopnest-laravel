<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /** Store category => dummyjson.com source categories. */
    private const CATEGORY_MAP = [
        'Electronics' => ['smartphones', 'laptops', 'tablets', 'mobile-accessories'],
        "Men's Fashion" => ['mens-shirts', 'mens-shoes', 'mens-watches'],
        "Women's Fashion" => ['womens-dresses', 'womens-shoes', 'womens-bags', 'womens-jewellery', 'womens-watches', 'tops'],
        'Beauty & Fragrance' => ['beauty', 'fragrances', 'skin-care'],
        'Home & Living' => ['furniture', 'home-decoration', 'kitchen-accessories'],
        'Sports & Outdoors' => ['sports-accessories', 'sunglasses'],
    ];

    private const DESCRIPTIONS = [
        'Electronics' => 'Shop the latest smartphones, laptops and accessories.',
        "Men's Fashion" => 'Shirts, shoes and watches for every occasion.',
        "Women's Fashion" => 'Dresses, bags, jewellery and more.',
        'Beauty & Fragrance' => 'Makeup, skincare and signature scents.',
        'Home & Living' => 'Furniture, decor and kitchen essentials.',
        'Sports & Outdoors' => 'Gear up for the game and the outdoors.',
    ];

    public function run(): void
    {
        User::create([
            'name' => 'Store Admin',
            'email' => 'admin@shop.test',
            'password' => 'password',
            'is_admin' => true,
        ])->forceFill(['is_admin' => true])->save();

        $customers = User::factory(25)->create()->each(function (User $user) {
            $user->update([
                'phone' => '+92 3'.fake()->numerify('## #######'),
                'city' => fake()->randomElement(['Lahore', 'Karachi', 'Islamabad', 'Rawalpindi', 'Faisalabad', 'Multan', 'Peshawar']),
                'address' => fake()->streetAddress(),
                'created_at' => now()->subDays(rand(1, 90)),
            ]);
        });

        $customers->push(User::create([
            'name' => 'Demo Customer',
            'email' => 'customer@shop.test',
            'password' => 'password',
            'phone' => '+92 300 1234567',
            'city' => 'Lahore',
            'address' => '12 Main Boulevard, Gulberg',
        ]));

        $this->seedCatalog();
        $this->seedCoupons();
        $this->seedOrders($customers);
        $this->seedReviews($customers);
    }

    private function seedCatalog(): void
    {
        // file_get_contents rather than the Http client: WAMP's PHP ships without a cURL CA bundle.
        $remote = rescue(fn () => json_decode(file_get_contents(
            'https://dummyjson.com/products?limit=0',
            context: stream_context_create(['http' => ['timeout' => 30]]),
        ), true)['products'] ?? [], [], false);

        $sort = 0;
        foreach (self::CATEGORY_MAP as $name => $sources) {
            $category = Category::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => self::DESCRIPTIONS[$name],
                'sort_order' => $sort++,
            ]);

            $items = collect($remote)->whereIn('category', $sources)->values();

            foreach ($items as $i => $item) {
                $brand = filled($item['brand'] ?? null)
                    ? Brand::firstOrCreate(['slug' => Str::slug($item['brand'])], ['name' => $item['brand']])
                    : null;

                $price = max(199, round($item['price'] * 280 / 10) * 10 - 1);
                $discount = (float) ($item['discountPercentage'] ?? 0);

                Product::create([
                    'category_id' => $category->id,
                    'brand_id' => $brand?->id,
                    'name' => $item['title'],
                    'slug' => Str::slug($item['title']).'-'.$item['id'],
                    'sku' => $item['sku'] ?? 'SKU-'.$item['id'],
                    'short_description' => Str::limit($item['description'], 160),
                    'description' => '<p>'.e($item['description']).'</p>'
                        .'<ul><li><strong>Brand:</strong> '.e($item['brand'] ?? 'Generic').'</li>'
                        .'<li><strong>Warranty:</strong> '.e($item['warrantyInformation'] ?? 'No warranty').'</li>'
                        .'<li><strong>Shipping:</strong> '.e($item['shippingInformation'] ?? 'Ships in 3-5 days').'</li>'
                        .'<li><strong>Returns:</strong> '.e($item['returnPolicy'] ?? '7 days return policy').'</li></ul>',
                    'price' => $price,
                    'compare_price' => $discount >= 8 ? round($price / (1 - $discount / 100) / 10) * 10 - 1 : null,
                    'stock' => $i % 11 === 0 ? rand(0, 4) : rand(8, 120),
                    'image' => $item['images'][0] ?? $item['thumbnail'],
                    'gallery' => array_slice($item['images'] ?? [], 1),
                    'is_featured' => $i < 2 || ($item['rating'] ?? 0) >= 4.7,
                ]);
            }

            $category->update(['image' => $category->products()->value('image')]);
        }

        if (Product::count() === 0) {
            $this->command?->warn('Could not reach dummyjson.com — seeding placeholder products without images.');
            Category::all()->each(fn (Category $category) => collect(range(1, 6))->each(fn ($n) => Product::create([
                'category_id' => $category->id,
                'name' => "{$category->name} Item {$n}",
                'slug' => Str::slug("{$category->name} item {$n}"),
                'short_description' => fake()->sentence(12),
                'description' => '<p>'.fake()->paragraph(4).'</p>',
                'price' => rand(10, 300) * 50 - 1,
                'stock' => rand(5, 60),
                'is_featured' => $n <= 2,
            ])));
        }
    }

    private function seedCoupons(): void
    {
        Coupon::create(['code' => 'WELCOME10', 'type' => 'percent', 'value' => 10]);
        Coupon::create(['code' => 'FLAT500', 'type' => 'fixed', 'value' => 500, 'min_amount' => 3000]);
        Coupon::create(['code' => 'MEGA25', 'type' => 'percent', 'value' => 25, 'min_amount' => 10000, 'max_uses' => 50, 'expires_at' => now()->addMonth()]);
    }

    private function seedOrders($customers): void
    {
        $products = Product::where('stock', '>', 0)->get();
        $statuses = ['delivered' => 45, 'shipped' => 15, 'processing' => 15, 'pending' => 15, 'cancelled' => 10];

        for ($n = 0; $n < 90; $n++) {
            $customer = $customers->random();
            $date = now()->subMinutes(rand(20, 59 * 24 * 60));
            $status = $this->weightedRandom($statuses);
            $lines = $products->random(rand(1, 4));

            $subtotal = 0;
            $items = $lines->map(function (Product $product) use (&$subtotal) {
                $qty = rand(1, 3);
                $subtotal += $product->price * $qty;

                return ['product_id' => $product->id, 'product_name' => $product->name, 'price' => $product->price, 'quantity' => $qty, 'total' => $product->price * $qty];
            });

            $shipping = $subtotal >= config('shop.free_shipping_over') ? 0 : config('shop.shipping_fee');
            $method = rand(1, 10) > 3 ? 'cod' : 'bank';

            $order = Order::create([
                'order_number' => 'ORD-'.$date->format('ymd').'-'.Str::upper(Str::random(5)),
                'user_id' => $customer->id,
                'status' => $status,
                'payment_method' => $method,
                'payment_status' => $status === 'delivered' || ($method === 'bank' && $status !== 'cancelled') ? 'paid' : 'unpaid',
                'subtotal' => $subtotal,
                'shipping' => $shipping,
                'total' => $subtotal + $shipping,
                'customer_name' => $customer->name,
                'customer_email' => $customer->email,
                'customer_phone' => $customer->phone ?? '+92 300 0000000',
                'address' => $customer->address ?? fake()->streetAddress(),
                'city' => $customer->city ?? 'Lahore',
            ]);
            $order->items()->createMany($items->all());
            $order->forceFill(['created_at' => $date, 'updated_at' => $date])->saveQuietly();
        }
    }

    private function seedReviews($customers): void
    {
        $comments = [
            5 => ['Excellent quality, exactly as shown. Highly recommended!', 'Fast delivery and great packaging. Love it!', 'Best purchase this year. Worth every rupee.'],
            4 => ['Very good product, delivery took a little longer than expected.', 'Good value for money. Would buy again.'],
            3 => ['Decent product, but could be better for the price.', 'Okay overall. Packaging was average.'],
        ];

        Product::inRandomOrder()->take(40)->get()->each(function (Product $product) use ($customers, $comments) {
            $customers->random(rand(1, 4))->each(function (User $user) use ($product, $comments) {
                $rating = $this->weightedRandom([5 => 55, 4 => 30, 3 => 15]);
                Review::create([
                    'product_id' => $product->id,
                    'user_id' => $user->id,
                    'rating' => $rating,
                    'comment' => fake()->randomElement($comments[$rating]),
                    'is_approved' => rand(1, 10) > 2,
                ]);
            });
        });
    }

    private function weightedRandom(array $weights): string|int
    {
        $roll = rand(1, array_sum($weights));
        foreach ($weights as $value => $weight) {
            if (($roll -= $weight) <= 0) {
                return $value;
            }
        }

        return array_key_first($weights);
    }
}
