@extends('layouts.app')

@section('content')
    <div class="container mx-auto max-w-7xl bg-white shadow-lg rounded-lg p-6 mt-8">
        <form action ="{{ route('admin.products.store') }}" method ="POST" enctype = "multipart/form-data">
            @csrf
            <div class="mb-4">
                <label for="name" class="block text-gray-700 font-bold mb-2">nazwa</label>
                <input type = 'text' name = 'name'
                    class="border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:border-blue-500">
            </div>
            <div class="mb-4">
                <label for="Slug" class="block text-gray-700 font-bold mb-2">Slug</label>
                <input type = 'text' name = 'slug'
                    class="border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:border-blue-500">
            </div>
            <div class="mb-4">
                <label for="Cena" class="block text-gray-700 font-bold mb-2">Cena w groszach</label>
                <input type = 'number' name = 'price'
                    class="border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:border-blue-500" min = '0'>
            </div>
            <div class="mb-4">
                <label for="Jednostka" class="block text-gray-700 font-bold mb-2">Jednostka</label>
                <input type = 'text' name = 'unit'
                    class="border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:border-blue-500">
            </div>
            <div class="mb-4">
                <label for="weight_per_unit" class="block text-gray-700 font-bold mb-2">waga na jednostkę w g</label>
                <input type = 'text' name = 'weight_per_unit'
                    class="border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:border-blue-500">
            </div>
            <div class="mb-4">
                <label for="stock_quantity" class="block text-gray-700 font-bold mb-2">zapas magazynowy w g</label>
                <input type = 'text' name = 'stock_quantity'
                    class="border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:border-blue-500">
            </div>
            <div class="mb-4">
                <label for ='category_id' class="block text-gray-700 font-bold mb-2">Kategoria </label>
                <select name = 'category_id'
                    class="border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:border-blue-500">
                    @foreach ($categories as $category)
                        <option value ='{{ $category->id }}'>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label for="description" class="block text-gray-700 font-bold mb-2">Opis</label>
                <textarea name="description"
                    class="border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:border-blue-500"></textarea>
            </div>
            <div class="mb-4">
                <label for = 'image' class="block text-gray-700 font-bold mb-2">Zdjęcie</label>
                <input type = 'file' name = 'image' accept="image/*"
                    class="border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:border-blue-500">
            </div>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 mt-4 rounded shadow hover:bg-blue-600">Dodaj
                produkt</button>
        </form>
    </div>
@endsection
