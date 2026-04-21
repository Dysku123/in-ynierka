@extends('layouts.app')

@section('content')
    <div class="bg-white shadow-lg rounded-lg p-6 mb-8 max-w-2xl mx-auto">
        <h2 class="text-2xl font-bold mb-4 text-gray-800">Zarządzanie statusem</h2>
        <form action = '{{ route('admin.order.updateStatus', $order) }}' method = 'post'>
            @method('patch')
            @csrf
            <label for = 'status'>Status:</label>
            <select name = 'status' id = 'status' class="border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:border-blue-500">
                @foreach (\App\Enums\OrderStatus::cases() as $status)
                    <option value="{{ $status->value }}" @selected($order->status === $status)>
                        {{ $status->label() }}
                        </option>
                @endforeach
            </select>
            <button type = 'submit' class="bg-blue-500 px-4 py-2 mt-4 rounded shadow hover:bg-blue-600">Zmień status</button>
        </form>
    </div>
@endsection
