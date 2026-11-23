<?php

namespace App\Http\Controllers;

use App\Services\CatalogService;

class HomeController extends Controller
{
    public function index(CatalogService $catalogService)
    {
        $data = $catalogService->getHomePageData();
        return view('home', $data);
    }

    public function product(string $slug, CatalogService $catalogService)
    {
        $product = $catalogService->getProductBySlug($slug);
        return view('product_show', compact('product'));
    }
}
