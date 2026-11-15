@extends('layouts.app')

@section('content')
    <div class="card">
        <h2 class="section-title">Nowy kod rabatowy</h2>

        @if ($errors->any())
            <div class="alert alert--error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('admin.discounts.store') }}" method="POST" class="form">
            @csrf
            @include('admin.discounts._fields', ['discount' => null])
            <button type="submit" class="btn btn--blue">Dodaj kod</button>
        </form>
    </div>
@endsection
