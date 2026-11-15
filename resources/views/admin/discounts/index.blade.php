@extends('layouts.app')

@section('content')
    <div class="card">
        <h2 class="section-title">Kody rabatowe 🎟️</h2>

        @if (session('success'))
            <div class="alert alert--success">{{ session('success') }}</div>
        @endif

        <a href="{{ route('admin.discounts.create') }}" class="btn btn--green mb-4">Dodaj kod</a>

        <table class="table table--bordered">
            <thead>
                <tr>
                    <th>Kod</th>
                    <th>Typ</th>
                    <th>Wartość</th>
                    <th>Min. koszyk</th>
                    <th>Użycia</th>
                    <th>Ważny do</th>
                    <th>Status</th>
                    <th>Akcje</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($codes as $code)
                    <tr>
                        <td><strong>{{ $code->code }}</strong></td>
                        <td>{{ $code->type === 'percent' ? 'procentowy' : 'kwotowy' }}</td>
                        <td>
                            @if ($code->type === 'percent')
                                {{ $code->value }}%
                            @else
                                {{ number_format($code->value / 100, 2, ',', ' ') }} zł
                            @endif
                        </td>
                        <td>{{ $code->min_cart_value ? number_format($code->min_cart_value / 100, 2, ',', ' ') . ' zł' : '—' }}</td>
                        <td>{{ $code->used_count }}{{ $code->max_uses ? ' / ' . $code->max_uses : '' }}</td>
                        <td>{{ $code->expires_at ? $code->expires_at->format('Y-m-d') : 'bezterminowy' }}</td>
                        <td>{{ $code->is_active ? 'aktywny' : 'wyłączony' }}</td>
                        <td>
                            <div style="display:flex; align-items:center; gap:0.5rem;">
                                <a href="{{ route('admin.discounts.edit', $code) }}" class="btn btn--blue">Edytuj</a>
                                <form action="{{ route('admin.discounts.destroy', $code) }}" method="post"
                                    onsubmit="return confirm('Usunąć ten kod rabatowy?');">
                                    @method('delete')
                                    @csrf
                                    <button type="submit" class="btn btn--danger">Usuń</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4">
            {{ $codes->links() }}
        </div>
    </div>
@endsection
