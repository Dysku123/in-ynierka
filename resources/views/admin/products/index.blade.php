@extends('layouts.app')

@section('content')
    <div class="container mx-auto max-w-7xl bg-white shadow-lg rounded-lg p-6 mt-8">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">Zarządzanie asortymentem</h2>
        <a href="{{ route('admin.products.create') }}"
            class="bg-green-500 text-white px-4 py-2 rounded shadow hover:bg-green-600 inline-block mb-4">Dodaj produkt</a>
        <table class = 'table-auto w-full'>
            <thead class="bg-gray-100 text-left text-gray-600 border-b-2 border-gray-200">
                <tr>
                    <th class = 'px-4 py-2'>ID</th>
                    <th class = 'px-4 py-2'>Nazwa</th>
                    <th class = 'px-4 py-2'>Kategoria</th>
                    <th class = 'px-4 py-2'>Cena</th>
                    <th class = 'px-4 py-2'>Zapas</th>
                    <th class = 'px-4 py-2'>Akcje</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                    <tr>
                        <td class="border px-4 py-2">{{ $product->id }}</td>
                        <td class="border px-4 py-2">{{ $product->name }}</td>
                        <td class="border px-4 py-2">{{ $product->category?->name ?? 'brak kategorii' }}</td>
                        <td class="border px-4 py-2">{{ number_format($product->price / 100, 2, ',', ' ') }} zł</td>
                        <td class="border px-4 py-2">{{ $product->stock_quantity }}</td>
                        <td class="border px-4 py-2">
                            <div class = 'flex items-center gap-2'>
                                <a href="{{ route('admin.products.edit', $product) }}"
                                class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">Edytuj</a>
                            <form action = "{{ route('admin.products.destroy', $product) }}" method = 'post'
                                class ="inline-block" onsubmit="return confirm('Czy na pewno chcesz usunąć ten produkt?');">
                                {{-- js do nie usuwania przypadkiem --}}
                                @method('delete')
                                @csrf
                                <button type="submit"
                                    class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded">Usuń</button>
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
