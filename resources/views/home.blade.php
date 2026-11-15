@extends('layouts.app')

@section('content')
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

    <div class="card">
        <h1 class="title">Witaj w sklepie: {{ $shopName }}</h1>
        <p class="subtitle">Wybierz kategorię swoich ulubionych serów:</p>

        <div class="grid grid--3 mt-6">
            @foreach ($categories as $category)
                <div class="category-card">
                    <h2 class="category-card__title">{{ $category->name }}</h2>
                    <a href="{{ route('shop.index', ['category' => $category->slug]) }}" class="category-card__link">Zobacz produkty &rarr;</a>
                </div>
            @endforeach
        </div>
    </div>

    <div class="mt-6">
        <h2 class="section-title">Nowości w ofercie 🆕</h2>
        <div class="grid grid--3 mt-4">
            @foreach ($products as $product)
                <div class="product-card">
                    <h3 class="product-card__name">{{ $product->name }}</h3>
                    <p class="product-card__price">{{ number_format($product->price / 100, 2, ',', ' ') }} zł</p>
                    <a href="{{ route('product.show', $product->slug) }}" class="btn btn--dark btn--block">Szczegóły</a>
                    <form method="POST" action="{{ route('cart.add') }}">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="portion_weight" value="{{ $product->min_portion }}">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="btn btn--yellow btn--block">+ Do koszyka ({{ $product->min_portion }} g)</button>
                    </form>
                </div>
            @endforeach
        </div>
    </div>

    @auth
        @if (auth()->user()->role->slug === 'admin')
            <div class="admin-panel">
                <p class="admin-panel__title">Panel Admina — witaj, {{ auth()->user()->name }}! 🧀</p>
                <div class="admin-panel__actions">
                    <a href="{{ route('admin.products.index') }}" class="btn btn--blue">Zarządzaj produktami</a>
                    <a href="{{ route('admin.products.create') }}" class="btn btn--green">+ Dodaj nowy ser</a>
                    <a href="{{ route('admin.order.index') }}" class="btn btn--dark">Zamówienia</a>
                    <a href="{{ route('admin.discounts.index') }}" class="btn btn--yellow">Kody rabatowe 🎟️</a>
                </div>
            </div>
        @endif
    @endauth
@endsection
