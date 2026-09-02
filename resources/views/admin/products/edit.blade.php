@extends('layouts.app')

@section('content')
    <div class="card">
        <h2 class="section-title">Edycja produktu</h2>

        @if ($errors->any())
            <div class="alert alert--error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="form">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label class="form-label">Nazwa</label>
                <input type="text" name="name" value="{{ old('name', $product->name) }}" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Slug</label>
                <input type="text" name="slug" value="{{ old('slug', $product->slug) }}" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Cena w groszach</label>
                <input type="number" name="price" min="0" value="{{ old('price', $product->price) }}" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Jednostka</label>
                <input type="text" name="unit" value="{{ old('unit', $product->unit) }}" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Waga na jednostkę (w g)</label>
                <input type="text" name="weight_per_unit" value="{{ old('weight_per_unit', $product->weight_per_unit) }}" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Zapas magazynowy (w g)</label>
                <input type="number" name="stock_quantity" min="0" value="{{ old('stock_quantity', $product->stock_quantity) }}" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Minimalna porcja (w g)</label>
                <input type="number" name="min_portion" min="1" value="{{ old('min_portion', $product->min_portion) }}" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Kategoria</label>
                <select name="category_id" class="form-select">
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Opis</label>
                <textarea name="description" class="form-textarea">{{ old('description', $product->description) }}</textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Zdjęcie</label>
                <input type="file" name="image" accept="image/*" class="form-input">
            </div>
            <button type="submit" class="btn btn--blue">Zapisz zmiany</button>
        </form>
    </div>
@endsection
