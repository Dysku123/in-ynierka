@extends('layouts.app')

@section('content')
    @if (session('success'))
        <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif
    <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-yellow-100">
        <div class="md:flex">
            <div class="md:w-1/2 bg-yellow-50 flex items-center justify-center relative">
                @if ($product->image_path)
                    <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}"
                        class="object-cover w-full h-full absolute inset-0">
                @else
                    <div class="p-12">
                        <span class="text-9xl">🧀</span>
                    </div>
                @endif
            </div>

            <div class="p-8 md:w-1/2">
                <div class="uppercase tracking-wide text-sm text-yellow-600 font-semibold">
                    {{ $product->category->name }}
                </div>
                <h1 class="text-4xl font-bold text-gray-900 mt-2">{{ $product->name }}</h1>

                <p class="mt-4 text-gray-600 leading-relaxed">
                    {{ $product->description }}
                </p>

                <div class="mt-6 flex items-center">
                    <span class="text-3xl font-bold text-gray-900">
                        {{ number_format($product->price / 100, 2, ',', ' ') }} zł
                    </span>
                    <span class="ml-2 text-gray-500">/ 100g</span>
                </div>

                <div class="mt-8">
                    <form method="POST" action="{{ route('cart.add') }}">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">

                        <div class="mb-6">
                            <p class = "text-sm text-gray-700">
                                Minimalna porcja: <span class= "font-semibold">{{ $product->min_portion }}g</span>
                            </p>
                            <p class = "text-xs text-gray-500 mt-1">Zamawiasz wielokrotność tej porcji - wybierz liczbę
                                porcji poniżej.</p>
                            <input type="hidden" name = "portion_weight" value ="{{ $product->min_portion }}">
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Liczba porcji:</label>
                            <div class="flex items-center gap-4">
                                <div class="flex items-center border border-gray-300 rounded-lg">
                                    <button type="button" id="minus"
                                        class="px-4 py-3 hover:bg-gray-100 rounded-l-lg">-</button>
                                    <input type="number" name="quantity" id="qty" value="1"
                                        class="w-12 text-center border-none focus:ring-0" readonly>
                                    <button type="button" id="plus"
                                        class="px-4 py-3 hover:bg-gray-100 rounded-r-lg">+</button>
                                </div>
                                <p class="text-gray-700">Razem: <span id="total-grams"
                                        class="font-semibold">{{ $product->min_portion }}</span> g</p>
                            </div>
                        </div>

                        <button type="submit"
                            class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-3 px-8 rounded-lg shadow-lg transition">
                            Dodaj do koszyka 🛒
                        </button>

                    </form>
                </div>
            </div>
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
