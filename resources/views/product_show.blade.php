@extends('layouts.app')

@section('content')
    @if (session('success'))
        <div class="alert alert--success">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert--error">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="product-detail">
        <div class="product-detail__media">
            @if ($product->image_path)
                <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}">
            @else
                <span class="product-detail__emoji">🧀</span>
            @endif
        </div>

        <div class="product-detail__body">
            <div class="product-detail__category">{{ $product->category->name }}</div>
            <h1 class="product-detail__name">{{ $product->name }}</h1>
            <p class="muted mt-4">{{ $product->description }}</p>

            <div class="product-detail__price">
                {{ number_format($product->price / 100, 2, ',', ' ') }} zł
                <span class="muted" style="font-size:1rem;">/ 100g</span>
            </div>

            <form method="POST" action="{{ route('cart.add') }}" class="mt-6">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">

                <div class="form-group">
                    <p class="muted">Minimalna porcja: <strong>{{ $product->min_portion }} g</strong></p>
                    <p class="muted" style="font-size:0.8rem;">Zamawiasz wielokrotność tej porcji — wybierz liczbę porcji poniżej.</p>
                    <input type="hidden" name="portion_weight" value="{{ $product->min_portion }}">
                </div>

                <div class="form-group">
                    <label class="form-label">Liczba porcji:</label>
                    <div class="qty-stepper">
                        <div class="qty-stepper__box">
                            <button type="button" id="minus" class="qty-stepper__btn">-</button>
                            <input type="number" name="quantity" id="qty" value="1" class="qty-stepper__input" readonly>
                            <button type="button" id="plus" class="qty-stepper__btn">+</button>
                        </div>
                        <p class="muted">Razem: <strong id="total-grams">{{ $product->min_portion }}</strong> g</p>
                    </div>
                </div>

                @if ($product->inStock())
                    <button type="submit" class="btn btn--yellow btn--lg btn--block">Dodaj do koszyka 🛒</button>
                @else
                    <button type="button" class="btn btn--light btn--lg btn--block" disabled>Brak w magazynie</button>
                @endif
            </form>
        </div>
    </div>

    <script>
        const qtyInput = document.getElementById('qty');
        const totalEl = document.getElementById('total-grams');
        const minPortion = {{ $product->min_portion }};

        const getValidQuantity = () => {
            const val = parseInt(qtyInput.value);
            return isNaN(val) || val < 1 ? 1 : val;
        };

        const updateTotal = () => {
            totalEl.textContent = getValidQuantity() * minPortion;
        };

        document.getElementById('plus').addEventListener('click', () => {
            qtyInput.value = getValidQuantity() + 1;
            updateTotal();
        });
        document.getElementById('minus').addEventListener('click', () => {
            qtyInput.value = Math.max(1, getValidQuantity() - 1);
            updateTotal();
        });
    </script>
@endsection
