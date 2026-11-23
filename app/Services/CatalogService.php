<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;

class CatalogService
{
    private const LATEST_PRODUCTS_LIMIT = 3;

    public function getHomePageData() {
        $shopName = config('app.name', 'Gouda & Spółka');
        $categories = Category::all();
        $products = Product::latest()->limit(self::LATEST_PRODUCTS_LIMIT)->get();
        return [
            'shopName' => $shopName,
            'categories' => $categories,
            'products' => $products
        ];
    }

    public function getProductBySlug(string $slug){
        $product = Product::with('category')->where('slug', $slug)->firstOrFail();
        return $product;
    }
}
