# ShopNest — Laravel E-commerce

Laravel 13 store with a Filament 5 admin panel and a Tailwind 4 + Alpine.js storefront.

## Run it

Double-click `start-shop.bat`, or:

```bash
C:\wamp64\bin\php\php8.3.28\php.exe artisan serve --port=8002
```

(Port 8000 is used by another local project, so ShopNest runs on 8002.)

- Store: http://localhost:8002
- Admin: http://localhost:8002/admin

Demo logins (from `database/seeders/DatabaseSeeder.php`):

| Role     | Email               | Password   |
|----------|---------------------|------------|
| Admin    | admin@shop.test     | password   |
| Customer | customer@shop.test  | password   |

Demo coupons: `WELCOME10` (10%), `FLAT500` (Rs. 500 off over Rs. 3,000), `MEGA25` (25% off over Rs. 10,000).

## Features

**Storefront** — home page (hero, categories, featured, new arrivals, brands), shop with search / category / brand / price / stock / sale filters and sorting, product page with gallery, reviews and related products, AJAX add-to-cart, wishlist, session cart with coupons and free-shipping progress, guest or account checkout (Cash on Delivery / Bank Transfer), order confirmation, customer account with order history, order tracking and profile.

**Admin (`/admin`)** — dashboard (revenue, orders, customers, low stock, 30-day sales chart, latest orders, best sellers), products (images, gallery, pricing, stock, featured, bulk actions), categories, brands, orders (view, status updates, mark paid, filters), coupons, review moderation, customers. Cancelling an order puts its stock back.

## Switch to MySQL (WAMP)

The project uses SQLite out of the box (`database/database.sqlite`). To use MySQL:

1. Start WampServer and create a database named `ecommerce` in phpMyAdmin.
2. In `.env` set `DB_CONNECTION=mysql`, `DB_DATABASE=ecommerce`, `DB_USERNAME=root`, `DB_PASSWORD=`.
3. Run `php artisan migrate:fresh --seed`.

## Development

```bash
npm run dev     # Vite with hot reload (run alongside artisan serve)
npm run build   # production assets
```

Shop settings (name, currency, shipping fee, free-shipping threshold, phone, email) live in `.env` under `SHOP_*` — see `config/shop.php`.

The seeder pulls real product data and image URLs from dummyjson.com; without internet it falls back to placeholder products.
