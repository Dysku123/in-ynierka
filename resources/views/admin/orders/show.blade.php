@extends('layouts.app')

@section('content')
    <div class=container>
        <form action = '{{ route('admin.order.updateStatus', $order) }}' method = 'post'>
            @method('patch')
            @csrf
            <label for = 'status'>Status:</label>
            <select name = 'status' id = 'status'>
                @foreach (\App\Enums\OrderStatus::cases() as $status)
                    <<option value="{{ $status->value }}" @selected($order->status === $status)>
                        {{ $status->label() }}
                        </option>
                @endforeach
            </select>
            <button type = 'submit'>Zmień status</button>
        </form>
    </div>
@endsection
