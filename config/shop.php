<?php

return [
    'name' => env('SHOP_NAME', 'ShopNest'),
    'currency' => env('SHOP_CURRENCY', 'Rs.'),
    'shipping_fee' => (float) env('SHOP_SHIPPING_FEE', 250),
    'free_shipping_over' => (float) env('SHOP_FREE_SHIPPING_OVER', 5000),
    'phone' => env('SHOP_PHONE', '+92 300 0000000'),
    'email' => env('SHOP_EMAIL', 'support@shopnest.test'),
];
