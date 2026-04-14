@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h2 class="font-semibold text-2xl mb-6">Twój Koszyk 🛒</h2>

                @if (session('success'))
                    <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($cartItems->isEmpty())
                    <p class="text-gray-500">Twój koszyk jest pusty. Pora dodać trochę sera! 🧀</p>
                @else
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b">
                                <th class="p-3">Produkt</th>
                                <th class="p-3">Waga porcji</th>
                                <th class="p-3">Ilość</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cartItems as $item)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="p-3 font-bold">{{ $item->product->name }}</td>
                                    <td class="p-3">{{ $item->portion_weight }} g</td>
                                    <td class="p-3">{{ $item->quantity }} szt.</td>
                                    <td class="p-3">
                                        <form action="{{ route('cart.destroy', $item->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 font-bold hover:underline">
                                                Usuń
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-6 flex justify-end items-center gap-6">
                        <div class="text-xl font-bold text-gray-800">
                            Do zapłaty: {{ number_format($totalPrice, 2, ',', ' ') }} zł
                        </div>
                        <button
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition-colors">
                            Przejdź do kasy
                        </button>
                    </div>
                @endif

            </div>
        </div>
    </div>
@endsection
