<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with('category')->paginate(20);
        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'=> 'required|string',
            'slug'=> 'required|string',
            'description'=>'string|nullable',
            'unit' => 'string|nullable',
            'price' => 'required|numeric',
            'weight_per_unit' => 'numeric|required',
            'stock_quantity' => 'numeric|required',
            'category_id' => 'exists:categories,id|required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);
        $imagePath=null;
        if($request->hasFile('image')){
            $imagePath = $request->file('image')->store('products', 'public');
        }
        $validated['image_path'] = $imagePath;
        Product::create($validated);
        return redirect()->route('admin.products.index')->with('success', 'Produkt dodany pomyślnie!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $product = Product::with('category')->findOrFail($id);
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = Product::findOrFail($id);
        $validated = $request->validate([
            'name'=> 'required|string',
            'slug'=> 'required|string',
            'description'=>'string|nullable',
            'unit' => 'string|nullable',
            'price' => 'required|numeric',
            'weight_per_unit' => 'numeric|required',
            'stock_quantity' => 'numeric|required',
            'category_id' => 'exists:categories,id|required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);
            if($request->hasFile('image')){
                if($product->image_path){
                    Storage::disk('public')->delete($product->image_path);
                }
                $imagePath = $request->file('image')->store('products', 'public');
                $validated['image_path'] = $imagePath;
            }
        $product->update($validated);
        return redirect()->route('admin.products.index')->with('success', 'Produkt zaktualizowany pomyślnie!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
        if($product->image_path){
            Storage::disk('public')->delete($product->image_path);
        }
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Produkt usunięty pomyślnie!');
    }
}
