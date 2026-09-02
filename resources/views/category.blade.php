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

    <div class="mb-6">
        <a href="{{ route('home') }}" class="category-card__link">&larr; Powrót do kategorii</a>
    </div>

    <h1 class="title">Kategoria: {{ $category->name }}</h1>

    <div class="grid grid--3">
        @foreach ($category->products as $product)
            <div class="product-card">
                <h2 class="product-card__name">{{ $product->name }}</h2>
                <p class="muted mb-2">{{ $product->description }}</p>
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
@endsection
