@extends('layouts.app')

@section('content')
    <div class="bg-white shadow-lg rounded-lg p-6 mb-8">
        <h1 class="text-4xl font-bold text-yellow-600 mb-4">Witaj w sklepie: {{ $shopName }}</h1>
        <p class="text-lg mb-4">Wybierz kategorię swoich ulubionych serów:</p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
            @foreach($categories as $category)
                <div class="bg-yellow-100 p-4 rounded border border-yellow-300 shadow hover:bg-yellow-200 transition">
                    <h2 class="text-xl font-bold text-yellow-800">{{ $category->name }}</h2>
                    <a href="/kategoria/{{ $category->slug }}" class="text-sm text-yellow-700 underline mt-2 block">Zobacz produkty &rarr;</a>
                </div>
            @endforeach
        </div>
    </div>

    <div class="mt-12">
        <h2 class="text-3xl font-bold text-gray-800 mb-6 border-b-2 border-yellow-400 pb-2 inline-block">Nowości w ofercie 🆕</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
            @foreach($products as $product)
                <div class="bg-white p-4 rounded-lg shadow border border-gray-100">
                    <h3 class="text-lg font-bold">{{ $product->name }}</h3>
                    <p class="text-yellow-700 font-semibold">{{ number_format($product->price / 100, 2, ',', ' ') }} zł</p>
                    <a href="/produkt/{{ $product->slug }}" class="mt-3 block text-center bg-gray-800 text-white py-2 rounded text-sm hover:bg-gray-700">Szczegóły</a>
                </div>
            @endforeach
        </div>
    </div>
    @auth
    @if(auth()->user()->admin)
        <div class="bg-blue-100 p-4 mb-4 rounded border border-blue-300">
            <p class="text-blue-800 font-bold">Panel Admina: Witaj, {{ auth()->user()->name }}!</p>
            <a href="#" class="bg-blue-500 text-white px-4 py-2 rounded shadow hover:bg-blue-600">
                + Dodaj nowy ser
            </a>
        </div>
    @endif
@endauth
@endsection
