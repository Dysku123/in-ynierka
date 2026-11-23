<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'category' => ['nullable', 'string', 'max:100'],
            'min_price' => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'max_price' => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'search' => ['nullable', 'string', 'max:100'],
        ], [], [
            'category' => 'kategoria',
            'min_price' => 'cena od',
            'max_price' => 'cena do',
            'search' => 'szukaj',
        ]);

        $query = Product::query()->with('category'); // produkty razem z kategorią (jedno dodatkowe zapytanie zamiast N)
        // filtr kategorii po slugu
        if ($request->filled('category')) {
            $query->whereHas('category', fn($q) => $q->where('slug', $request->category)); // funkcja strzałkowa: przyjmuje $q i zwraca wynik metody where
        }

        // filtr ceny (zł z formularza → grosze w bazie)
        if ($request->filled('min_price')) {
            $query->where('price', '>=', (int) round($request->min_price * 100));
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', (int) round($request->max_price * 100));
        }


        // wyszukiwanie po nazwie
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%'); //znaczek % oznacza, że może być dowolny ciąg znaków przed i po wyszukiwanej frazie
        }

        // sortowanie
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
