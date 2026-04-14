@extends('layouts.app')

@section('content')
<div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-yellow-100">
    <div class="md:flex">
        <div class="md:w-1/2 bg-yellow-100 flex items-center justify-center p-12">
            <span class="text-9xl">🧀</span>
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
                        <label for="portion_weight" class="block text-sm font-medium text-gray-700 mb-2">
                            Wybierz wielkość porcji:
                        </label>
                        <select name="portion_weight" id="portion_weight" class="w-full md:w-1/2 border-gray-300 rounded-lg focus:ring-yellow-500 focus:border-yellow-500">
                            <option value="100">100 g</option>
                            <option value="200">200 g</option>
                            <option value="300">300 g</option>
                            <option value="500">500 g</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="flex items-center border border-gray-300 rounded-lg">
                            <button type="button" id="minus" class="px-4 py-3 hover:bg-gray-100 rounded-l-lg">-</button>
                            <input type="number" name="quantity" id="qty" value="1" class="w-12 text-center border-none focus:ring-0" readonly>
                            <button type="button" id="plus" class="px-4 py-3 hover:bg-gray-100 rounded-r-lg">+</button>
                        </div>

                        <button type="submit" class="flex-grow bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-3 px-8 rounded-lg shadow-lg transition duration-300 transform hover:scale-105">
                            Dodaj do koszyka 🛒
                        </button>
                    </div>
                </form>
                </div>
        </div>
    </div>
</div>

<script>
    const qtyInput = document.getElementById('qty');
    document.getElementById('plus').addEventListener('click', () => {
        qtyInput.value = parseInt(qtyInput.value) + 1;
    });
    document.getElementById('minus').addEventListener('click', () => {
        if(qtyInput.value > 1) qtyInput.value = parseInt(qtyInput.value) - 1;
    });
</script>
@endsection
