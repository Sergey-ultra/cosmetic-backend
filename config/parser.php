<?php
return [
    'email' => env('PARSER_EMAIL'),
    'password' => env('PARSER_PASSWORD'),
    'parser_url' => env('PARSER_URL'),
    'login_url' => env('PARSER_URL'). '/api/login',
    'brand_url' => env('PARSER_URL'). '/api/brand',
    'category_url' => env('PARSER_URL'). '/api/category',
    'country_url' => env('PARSER_URL'). '/api/country',
    'image_url' => env('PARSER_URL'). '/api/image',
    'link_url' => env('PARSER_URL'). '/api/link-all',
    'product_url' => env('PARSER_URL').'/api/product-all',
    'sku_url' =>  env('PARSER_URL'). '/api/sku-all',
    'price_history_url' => env('PARSER_URL'). '/api/price-history',
    'current_price_url' => env('PARSER_URL'). '/api/current-price',
    'store_url' =>  env('PARSER_URL'). '/api/store',
    'ingredient_url' =>  env('PARSER_URL'). '/api/ingredient',
    'ingredient_product_url' =>  env('PARSER_URL'). '/api/ingredient-product',
    'change_sku_images_upload_statuses' => env('PARSER_URL'). '/api/sku/change-sku-images-upload-statuses'
];
