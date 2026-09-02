<x-mail::message>
# Dziękujemy za zamówienie!

Twoje zamówienie **#{{ $order->id }}** zostało przyjęte. 🧀

<x-mail::table>
| Produkt | Porcja | Ilość |
|:--------|:------:|:-----:|
@foreach ($order->items as $item)
| {{ $item->name }} | {{ $item->portion_weight }} g | {{ $item->quantity }} szt. |
@endforeach
</x-mail::table>

**Do zapłaty: {{ number_format($order->total_price / 100, 2, ',', ' ') }} zł**

Status: {{ $order->status->label() }}

@if ($order->shipping_name)
## Dostawa

**{{ $order->shipping_name }} {{ $order->shipping_surname }}**
{{ $order->shipping_street }} {{ $order->shipping_house_number }}@if ($order->shipping_apartment_number)/{{ $order->shipping_apartment_number }}@endif
{{ $order->shipping_postal_code }} {{ $order->shipping_city }}
tel. {{ $order->shipping_phone }}

Metoda dostawy: **{{ $order->delivery_method?->label() ?? '—' }}**
Metoda płatności: **{{ $order->payment_method?->label() ?? '—' }}**
@endif

Dziękujemy,<br>
{{ config('app.name') }}
</x-mail::message>
