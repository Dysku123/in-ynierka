@extends('layouts.app')

@section('content')
    <h1 class="section-title">Sklep 🧀</h1>

    @if (session('success'))
        <div class="alert alert--success">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert--error">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="GET" action="{{ route('shop.index') }}" class="shop-filters">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Szukaj sera..." class="form-input">
        <select name="category" class="form-select">
            <option value="">Wszystkie kategorie</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->slug }}" @selected(request('category') === $cat->slug)>{{ $cat->name }}</option>
            @endforeach
        </select>
        <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Cena od (zł)" class="form-input" min="0" step="0.01">
        <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Cena do (zł)" class="form-input" min="0" step="0.01">
        <select name="sort" class="form-select">
            <option value="">Najnowsze</option>
            <option value="price_asc" @selected(request('sort') === 'price_asc')>Cena rosnąco</option>
            <option value="price_desc" @selected(request('sort') === 'price_desc')>Cena malejąco</option>
            <option value="name" @selected(request('sort') === 'name')>Nazwa A-Z</option>
        </select>
        <button type="submit" class="btn btn--yellow">Filtruj</button>
        <a href="{{ route('shop.index') }}" class="btn btn--light">Wyczyść</a>
    </form>

    @if ($products->isEmpty())
        <p class="muted mt-6">Brak produktów spełniających kryteria.</p>
    @else
        <div class="grid grid--3 mt-6">
            @foreach ($products as $product)
                <div class="product-card">
                    <h3 class="product-card__name">{{ $product->name }}</h3>
                    <p class="muted" style="font-size:0.85rem;">{{ $product->category->name }}</p>
                    <p class="product-card__price">{{ number_format($product->price / 100, 2, ',', ' ') }} zł / 100g</p>
                    <a href="{{ route('product.show', $product->slug) }}" class="btn btn--dark btn--block">Szczegóły</a>
                    <form method="POST" action="{{ route('cart.add') }}">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="portion_weight" value="{{ $product->min_portion }}">
                        <input type="hidden" name="quantity" value="1">
                        @if ($product->inStock())
                            <button type="submit" class="btn btn--yellow btn--block">+ Do koszyka ({{ $product->min_portion }} g)</button>
                        @else
                            <button type="button" class="btn btn--light btn--block" disabled>Brak w magazynie</button>
                        @endif
                    </form>
                </div>
            @endforeach
        </div>
        <div class="mt-6">
            {{ $products->links() }}
        </div>
    @endif
@endsection
