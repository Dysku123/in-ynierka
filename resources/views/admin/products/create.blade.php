@extends('layouts.app')

@section('content')
    <div class="card">
        <h2 class="section-title">Nowy produkt</h2>

        @if ($errors->any())
            <div class="alert alert--error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="form">
            @csrf
            <div class="form-group">
                <label class="form-label">Nazwa</label>
                <input type="text" name="name" value="{{ old('name') }}" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Slug</label>
                <input type="text" name="slug" value="{{ old('slug') }}" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Cena w groszach</label>
                <input type="number" name="price" min="0" value="{{ old('price') }}" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Jednostka</label>
                <input type="text" name="unit" value="{{ old('unit', 'kg') }}" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Waga na jednostkę (w g)</label>
                <input type="text" name="weight_per_unit" value="{{ old('weight_per_unit') }}" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Zapas magazynowy (w g)</label>
                <input type="number" name="stock_quantity" min="0" value="{{ old('stock_quantity') }}" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Minimalna porcja (w g)</label>
                <input type="number" name="min_portion" min="1" value="{{ old('min_portion', 100) }}" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Kategoria</label>
                <select name="category_id" class="form-select">
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Opis</label>
                <textarea name="description" class="form-textarea">{{ old('description') }}</textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Zdjęcie</label>
                <input type="file" name="image" accept="image/*" class="form-input">
            </div>
            <button type="submit" class="btn btn--blue">Dodaj produkt</button>
        </form>
    </div>
@endsection
