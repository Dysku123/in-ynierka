<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Services\CatalogService;

class HomeController extends Controller
{
    public function index(CatalogService $catalogService)
    {
        $data = $catalogService->getHomePageData();
        return view('home', $data);
    }
    public function show(string $slug, CatalogService $catalogService)
    {
        $category = $catalogService->getCategoryWithProducts($slug);
        return view('category', compact('category'));
    }

    public function product(string $slug , CatalogService $catalogService)
    {
        $product = $catalogService->getProductBySlug($slug);
        return view('product_show', compact('product'));
    }
}
