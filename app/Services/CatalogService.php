<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;

class CatalogService
{

    public function getHomePageData() {
        $shopName = config('app.name', 'Gouda & Spółka');
        $categories = Category::all();
        $products = Product::latest()->limit(3)->get();
        return[
            'shopName' => $shopName,
            'categories' => $categories,
            'products' => $products
        ];
    }

    public function getCategoryWithProducts(string $slug){
        $category = Category::with('products')->where('slug', $slug)->firstOrFail(); //jezeli nie ma, 404, z relacji ciągniemy wszystko razem
        return $category;
    }

    public function getProductBySlug(string $slug){
        $product = Product::with('category')->where('slug', $slug)->firstOrFail();
        return $product;
    }
}
