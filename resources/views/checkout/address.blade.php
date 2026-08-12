@extends('layouts.app')
@section('content')
    <div class="max-w-2xl mx-auto py-10">
        <h1 class="text-2xl font-bold mb-6">Krok 1 z 3 — Dane do wysyłki</h1>

        {{-- Wyświetlanie błędów walidacji --}}
        @if ($errors->any())
            <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif
        @auth
            @if ($savedAddresses->isNotEmpty())
                <div class="mb-6">
                    <h2 class="font-semibold mb-2">Twoje zapisane adresy</h2>
                    <div class="grid sm:grid-cols-2 gap-3">
                        @foreach ($savedAddresses as $addr)
                            <button type="button" class="text-left p-3 border rounded-lg hover:bg-gray-50"
                                onclick="fillAddress(this)" data-name="{{ $addr->name }}" data-surname="{{ $addr->surname }}"
                                data-phone="{{ $addr->phone }}" data-street="{{ $addr->street }}"
                                data-postal_code="{{ $addr->postal_code }}" data-city="{{ $addr->city }}">
                                {{ $addr->name }} {{ $addr->surname }}<br>
                                <span class="text-sm text-gray-500">{{ $addr->street }}, {{ $addr->postal_code }}
                                    {{ $addr->city }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif
        @endauth

        <form method="POST" action="{{ route('checkout.address.store') }}" class="space-y-4">
            @csrf

            @guest {{-- pole e-mail tylko dla gościa --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Adres e-mail</label>
                    <input type="email" name="email" value="{{ old('email', $address['email'] ?? '') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
            @endguest

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Imię</label>
                    <input type="text" name="name" value="{{ old('name', $address['name'] ?? '') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nazwisko</label>
                    <input type="text" name="surname" value="{{ old('surname', $address['surname'] ?? '') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Telefon</label>
                <input type="text" name="phone" value="{{ old('phone', $address['phone'] ?? '') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Ulica i numer</label>
                <input type="text" name="street" value="{{ old('street', $address['street'] ?? '') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Kod pocztowy</label>
                    <input type="text" name="postal_code"
                        value="{{ old('postal_code', $address['postal_code'] ?? '') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Miasto</label>
                    <input type="text" name="city" value="{{ old('city', $address['city'] ?? '') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
            </div>
            @auth
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="save_address" value="1">
                    Zapisz ten adres na przyszłość
                </label>
            @endauth
            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition-colors">
                Dalej: dostawa →
            </button>
        </form>
    </div>
    <script>
        function fillAddress(btn) {
            const fields = ['name', 'surname', 'phone', 'street', 'postal_code', 'city'];
            fields.forEach(f => {
                const input = document.querySelector(`[name="${f}"]`);
                if (input) input.value = btn.dataset[f];
            });
        }
    </script>
@endsection
