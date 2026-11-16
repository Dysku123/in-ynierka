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

@if ($order->discount_amount > 0)
Rabat ({{ $order->discount_code }}): −{{ number_format($order->discount_amount / 100, 2, ',', ' ') }} zł
@endif

Dostawa: {{ $order->shipping_cost > 0 ? number_format($order->shipping_cost / 100, 2, ',', ' ') . ' zł' : 'gratis' }}

**Do zapłaty: {{ number_format($order->total_price / 100, 2, ',', ' ') }} zł**

Status: {{ $order->status->label() }}

@if ($order->shipping_name)
## Adres dostawy

**{{ $order->shipping_name }} {{ $order->shipping_surname }}**
{{ $order->shipping_street }} {{ $order->shipping_house_number }}@if ($order->shipping_apartment_number)/{{ $order->shipping_apartment_number }}@endif
{{ $order->shipping_postal_code }} {{ $order->shipping_city }}
tel. {{ $order->shipping_phone }}

Metoda dostawy: **{{ $order->delivery_method?->label() ?? '—' }}**
Metoda płatności: **{{ $order->payment_method?->label() ?? '—' }}**
@endif

@if ($order->payment_method?->value === 'transfer')
## Dane do przelewu

Prosimy o opłacenie zamówienia przelewem:

- **Odbiorca:** {{ config('shop.bank_recipient') }}
- **Numer konta:** {{ config('shop.bank_account') }}
- **Tytuł przelewu:** Zamówienie #{{ $order->id }}
- **Kwota:** {{ number_format($order->total_price / 100, 2, ',', ' ') }} zł
@endif

@if (!$order->user_id)
@php($trackUrl = \Illuminate\Support\Facades\URL::signedRoute('order.guest_show', ['uuid' => $order->uuid]))
## Śledzenie zamówienia

Status swojego zamówienia sprawdzisz w każdej chwili tutaj:

<x-mail::button :url="$trackUrl">
Zobacz moje zamówienie
</x-mail::button>
@endif

Dziękujemy,<br>
{{ config('app.name') }}
</x-mail::message>
