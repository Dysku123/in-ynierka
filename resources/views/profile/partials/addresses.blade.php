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
            <div>
                <p class="font-medium">{{$address->name}} {{$address->surname}}</p>
                <p class="text-sm text-gray-600">{{$address->street}}, {{$address->postal_code}} {{$address->city}}</p>
                <p class="text-sm text-gray-600">tel. {{$address->phone}}</p>
            </div>
            <form action="POST"></form>
        @empty

        @endforelse
    </div>


</section>
