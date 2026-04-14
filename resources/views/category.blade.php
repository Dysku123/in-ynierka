@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <a href="/" class="text-yellow-600 hover:underline">&larr; Powrót do kategorii</a>
    </div>

    <h1 class="text-3xl font-bold text-yellow-800 mb-6">Kategoria: {{ $category->name }}</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($category->products as $product)
            <div class="bg-white p-6 rounded-lg shadow-md border border-yellow-200">
                <h2 class="text-xl font-bold mb-2">{{ $product->name }}</h2>
                <p class="text-gray-600 text-sm mb-4">{{ $product->description }}</p>
                <div class="flex justify-between items-center mt-4">
                    <span class="text-lg font-bold text-yellow-700">
                        {{ number_format($product->price / 100, 2, ',', ' ') }} zł{{-- zmienamy grosze na plny --}}
                    </span>
                    <a href="/produkt/{{ $product->slug }}"
                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded shadow">
                        Szczegóły
                    </a>
                </div>
            </div>
        @endforeach
    </div>
@endsection
