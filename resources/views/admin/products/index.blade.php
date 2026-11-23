@extends('layouts.app')

@section('content')
    <div class="card">
        <h2 class="section-title">Zarządzanie asortymentem</h2>
        @if (session('success'))
            <div class="alert alert--success">{{ session('success') }}</div>
        @endif
        <a href="{{ route('admin.products.create') }}" class="btn btn--green mb-4">Dodaj produkt</a>

        <table class="table table--bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nazwa</th>
                    <th>Kategoria</th>
                    <th>Cena</th>
                    <th>Zapas</th>
                    <th>Akcje</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                    <tr>
                        <td>{{ $product->id }}</td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->category?->name ?? 'brak kategorii' }}</td>
                        <td>{{ number_format($product->price / 100, 2, ',', ' ') }} zł</td>
                        <td>{{ $product->stock_quantity }}</td>
                        <td>
                            <div style="display:flex; align-items:center; gap:0.5rem;">
                                <a href="{{ route('admin.products.edit', $product) }}" class="btn btn--blue">Edytuj</a>
                                <form action="{{ route('admin.products.destroy', $product) }}" method="post"
                                    onsubmit="return confirm('Czy na pewno chcesz usunąć ten produkt?');">
                                    @method('delete')
                                    @csrf
                                    <button type="submit" class="btn btn--danger">Usuń</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4">
            {{ $products->links() }}
        </div>
    </div>
@endsection
