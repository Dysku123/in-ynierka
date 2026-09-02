<section>
    <h2 class="subheading">Zapisane adresy</h2>
    <p class="muted mb-4">Adresy używane przy składaniu zamówienia.</p>

    @if (session('success'))
        <div class="alert alert--success">{{ session('success') }}</div>
    @endif

    @forelse (auth()->user()->addresses as $address)
        <div class="summary-box flex-between">
            <div>
                <p><strong>{{ $address->name }} {{ $address->surname }}</strong></p>
                <p class="muted">{{ $address->street }}, {{ $address->postal_code }} {{ $address->city }}</p>
                <p class="muted">tel. {{ $address->phone }}</p>
            </div>
            <form method="POST" action="{{ route('address.destroy', $address) }}"
                onsubmit="return confirm('Usunąć ten adres?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="link-danger">Usuń</button>
            </form>
        </div>
    @empty
        <p class="muted">Nie masz jeszcze zapisanych adresów.</p>
    @endforelse
</section>
