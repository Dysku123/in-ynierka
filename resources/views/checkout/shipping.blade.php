@extends('layouts.app')
@section('content')
    <div class="max-w-2xl mx-auto py-10">
        <h1 class="text-2xl font-bold mb-6">Krok 2 z 3 — Dostawa i płatność</h1>

        @if ($errors->any())
            <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('checkout.shipping.store') }}" class="space-y-8">
            @csrf
            <div>
                <h2 class="font-semibold text-lg mb-3">Metoda dostawy</h2>
                <div class="space-y-2">
                    @foreach ($deliveryMethods as $method)
                        <label class="flex items-center gap-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="delivery_method" value="{{ $method->value }}"
                                @checked(($checkout['delivery_method'] ?? '') === $method->value)>
                            <span>{{ $method->label() }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div>
                <h2 class="font-semibold text-lg mb-3">Metoda płatności</h2>
                <div class="space-y-2">
                    @foreach ($paymentMethods as $method)
                        <label class="flex items-center gap-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="payment_method" value="{{ $method->value }}"
                                @checked(($checkout['payment_method'] ?? '') === $method->value)>
                            <span>{{ $method->label() }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="flex justify-between items-center">
                <a href="{{ route('checkout.address') }}" class="text-gray-600 hover:underline">← Wstecz</a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg">
                    Dalej: podsumowanie →
                </button>
            </div>
        </form>
    </div>
@endsection
