<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">Zapisane adresy</h2>
        <p class="mt-1 text-sm text-gray-600">Adresy używane przy składaniu zamówienia.</p>
    </header>

    @if (session('success'))
        <div class="mt-4 p-3 text-sm text-green-700 bg-green-100 rounded-lg">{{ session('success') }}</div>
    @endif

    <div class="mt-6 space-y-4">
        @forelse (auth()->user()->addresses as $address)
            <div class="flex justify-between items-start p-4 border rounded-lg">
                <div>
                    <p class="font-medium">{{ $address->name }} {{ $address->surname }}</p>
                    <p class="text-sm text-gray-600">{{ $address->street }}, {{ $address->postal_code }} {{ $address->city }}</p>
                    <p class="text-sm text-gray-600">tel. {{ $address->phone }}</p>
                </div>
                <form method="POST" action="{{ route('address.destroy', $address) }}"
                    onsubmit="return confirm('Usunąć ten adres?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:underline text-sm">Usuń</button>
                </form>
            </div>
        @empty
            <p class="text-sm text-gray-500">Nie masz jeszcze zapisanych adresów.</p>
        @endforelse
    </div>
</section>
