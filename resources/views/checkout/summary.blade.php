@extends('layouts.app')
@section('content')
    <div class="max-w-3xl mx-auto py-10">
        <h1 class="text-2xl font-bold mb-6">Krok 3 z 3 — Podsumowanie zamówienia</h1>

        <div class="grid md:grid-cols-2 gap-6 mb-8">
            <div class="bg-white p-5 rounded-lg shadow-sm border">
                <h2 class="font-semibold text-lg mb-3">Dane dostawy</h2>
                <p>{{ $address['name'] }} {{ $address['surname'] }}</p>
                <p>{{ $address['street'] }}</p>
                <p>{{ $address['postal_code'] }} {{ $address['city'] }}</p>
                <p>tel. {{ $address['phone'] }}</p>
                @isset($address['email'])
                    <p class="text-gray-600 text-sm mt-1">{{ $address['email'] }}</p>
                @endisset
            </div>

            <div class="bg-white p-5 rounded-lg shadow-sm border">
                <h2 class="font-semibold text-lg mb-3">Dostawa i płatność</h2>
                <p><span class="text-gray-500">Dostawa:</span> {{ $delivery->label() }}</p>
                <p><span class="text-gray-500">Płatność:</span> {{ $payment->label() }}</p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-lg shadow-sm border mb-6">
            <h2 class="font-semibold text-lg mb-3">Twoje produkty</h2>
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b text-sm text-gray-500">
                        <th class="py-2">Produkt</th>
                        <th class="py-2">Porcja</th>
                        <th class="py-2">Ilość</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cartItems as $item)
                        <tr class="border-b">
                            <td class="py-2 font-medium">{{ $item->product->name }}</td>
                            <td class="py-2">{{ $item->portion_weight }} g</td>
                            <td class="py-2">{{ $item->quantity }} szt.</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="text-right text-xl font-bold mt-4">
                Do zapłaty: {{ number_format($totalPrice / 100, 2, ',', ' ') }} zł
            </div>
        </div>

        <form method="POST" action="{{ route('checkout.place') }}">
            @csrf
            <div class="flex justify-between items-center">
                <a href="{{ route('checkout.shipping') }}" class="text-gray-600 hover:underline">← Wstecz</a>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-8 rounded-lg">
                    Zamawiam i płacę 🧀
                </button>
            </div>
        </form>
    </div>
@endsection
