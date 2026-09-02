@extends('layouts.app')

@section('content')
    <div class="container-narrow">
        <h1 class="section-title">Krok 1 z 3 — Dane do wysyłki</h1>

        @if ($errors->any())
            <div class="alert alert--error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @auth
            @if ($savedAddresses->isNotEmpty())
                <div class="mb-6">
                    <h2 class="form-label">Twoje zapisane adresy</h2>
                    <div class="saved-addresses">
                        @foreach ($savedAddresses as $addr)
                            <button type="button" class="saved-address" onclick="fillAddress(this)"
                                data-name="{{ $addr->name }}" data-surname="{{ $addr->surname }}"
                                data-phone="{{ $addr->phone }}" data-street="{{ $addr->street }}"
                                data-house_number="{{ $addr->house_number }}" data-apartment_number="{{ $addr->apartment_number }}"
                                data-postal_code="{{ $addr->postal_code }}" data-city="{{ $addr->city }}">
                                {{ $addr->name }} {{ $addr->surname }}<br>
                                <span class="muted" style="font-size:0.8rem;">{{ $addr->street }} {{ $addr->house_number }}{{ $addr->apartment_number ? '/' . $addr->apartment_number : '' }}, {{ $addr->postal_code }} {{ $addr->city }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif
        @endauth

        <form method="POST" action="{{ route('checkout.address.store') }}" class="form">
            @csrf

            @guest
                <div class="form-group">
                    <label class="form-label">Adres e-mail</label>
                    <input type="email" name="email" value="{{ old('email', $address['email'] ?? '') }}" class="form-input">
                </div>
            @endguest

            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label">Imię</label>
                    <input type="text" name="name" value="{{ old('name', $address['name'] ?? '') }}" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Nazwisko</label>
                    <input type="text" name="surname" value="{{ old('surname', $address['surname'] ?? '') }}" class="form-input">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Telefon</label>
                <input type="text" name="phone" value="{{ old('phone', $address['phone'] ?? '') }}" class="form-input">
            </div>

            <div class="form-group">
                <label class="form-label">Ulica</label>
                <input type="text" name="street" value="{{ old('street', $address['street'] ?? '') }}" class="form-input">
            </div>

            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label">Nr domu</label>
                    <input type="text" name="house_number" value="{{ old('house_number', $address['house_number'] ?? '') }}" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Nr mieszkania <span class="muted">(opcjonalnie)</span></label>
                    <input type="text" name="apartment_number" value="{{ old('apartment_number', $address['apartment_number'] ?? '') }}" class="form-input">
                </div>
            </div>

            <div class="form-grid-2">
                <div class="form-group">
                    <label class="form-label">Kod pocztowy</label>
                    <input type="text" name="postal_code" value="{{ old('postal_code', $address['postal_code'] ?? '') }}" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Miasto</label>
                    <input type="text" name="city" value="{{ old('city', $address['city'] ?? '') }}" class="form-input">
                </div>
            </div>

            @auth
                <label class="form-check">
                    <input type="checkbox" name="save_address" value="1">
                    Zapisz ten adres na przyszłość
                </label>
            @endauth

            <button type="submit" class="btn btn--blue btn--block">Dalej: dostawa →</button>
        </form>
    </div>

    <script>
        function fillAddress(btn) {
            const fields = ['name', 'surname', 'phone', 'street', 'house_number', 'apartment_number', 'postal_code', 'city'];
            fields.forEach(f => {
                const input = document.querySelector(`[name="${f}"]`);
                if (input) input.value = btn.dataset[f];
            });
        }
    </script>
@endsection
