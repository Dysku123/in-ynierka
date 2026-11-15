<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()->with('category'); //pobieranie produktów z relacją category
        //po slug
        if ($request->filled('category')) {
            $query->whereHas('category', fn($q) => $q->where('slug', $request->category)); //funckja strzalkwa, która przyjmuje $q i zwraca wynik metody where
        }

        //cena
        if ($request->filled('min_price')) {
            $query->where('price', '>=', (int) ($request->min_price * 100));
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', (int) ($request->max_price * 100));
        }


        //nazwa

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%'); //znaczek % oznacza, że może być dowolny ciąg znaków przed i po wyszukiwanej frazie
        }

        //sortowanie
        match ($request->get('sort')) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'name' => $query->orderBy('name'),
            default => $query->latest(),
        };
        $products = $query->paginate(12)->withQueryString(); //withQueryString() pozwala na zachowanie parametrów zapytania w paginacji, a paginacja to podział wyników na strony, w tym przypadku po 12 produktów na stronę
        $categories = Category::all();
        return view('shop', compact('products', 'categories'));
    }
}
