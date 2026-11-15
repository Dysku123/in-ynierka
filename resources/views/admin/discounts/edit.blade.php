@extends('layouts.app')

@section('content')
    <div class="card">
        <h2 class="section-title">Edytuj kod: {{ $discount->code }}</h2>

        @if ($errors->any())
            <div class="alert alert--error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('admin.discounts.update', $discount) }}" method="POST" class="form">
            @csrf
            @method('PUT')
            @include('admin.discounts._fields', ['discount' => $discount])
            <button type="submit" class="btn btn--blue">Zapisz zmiany</button>
        </form>
    </div>
@endsection
