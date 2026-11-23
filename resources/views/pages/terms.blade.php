@extends('layouts.app')

@section('content')
    <div class="container-narrow">
        <div class="card">
            <h1 class="section-title">Regulamin sklepu</h1>
            <p class="muted">Obowiązuje od 23 listopada 2026 r.</p>

            <div class="summary-box mt-4">
                {{ config('app.name') }} jest sklepem <strong>demonstracyjnym</strong>, przygotowanym jako projekt
                inżynierski. Zamówienia nie są realizowane, a płatności online działają w trybie testowym operatora.
            </div>

            <h2 class="subheading mt-6">§ 1. Postanowienia ogólne</h2>
            <p>Regulamin określa zasady korzystania ze sklepu internetowego {{ config('app.name') }}, składania zamówień,
                płatności, dostawy, odstąpienia od umowy i reklamacji. Kontakt ze sklepem:
                <strong>{{ config('mail.from.address') }}</strong>.</p>
            <p>Do korzystania ze sklepu wystarczy aktualna przeglądarka internetowa z włączoną obsługą plików cookies
                i JavaScriptu.</p>

            <h2 class="subheading mt-6">§ 2. Konto klienta</h2>
            <p>Zakupy można zrobić bez zakładania konta (jako gość) albo po rejestracji adresem e-mail i hasłem lub przez
                konto Google. Konto pozwala zapisywać adresy dostawy i przeglądać historię zamówień.</p>
            <p>Klient może w każdej chwili usunąć konto w ustawieniach profilu. Dane osobowe w jego zamówieniach są wtedy
                anonimizowane, a w sklepie zostają jedynie dane potrzebne do rozliczeń (kwoty, produkty, daty).</p>

            <h2 class="subheading mt-6">§ 3. Składanie zamówień</h2>
            <p>Klient dodaje produkty do koszyka, a następnie w trzech krokach podaje adres dostawy (opcjonalnie dane do
                faktury), wybiera sposób dostawy i płatności oraz sprawdza podsumowanie.</p>
            <p>Umowa sprzedaży zostaje zawarta w chwili kliknięcia przycisku <strong>„Zamawiam z obowiązkiem
                    zapłaty”</strong>, po zaakceptowaniu regulaminu. Potwierdzenie zamówienia sklep wysyła na podany adres
                e-mail.</p>

            <h2 class="subheading mt-6">§ 4. Ceny i płatności</h2>
            <p>Ceny podane są w złotych polskich, zawierają podatek VAT i dotyczą <strong>100 g</strong> produktu. Produkty
                sprzedawane są w porcjach o minimalnej wadze podanej przy każdym serze.</p>
            <p>Dostępne metody płatności:</p>
            <ul>
                <li>płatność online kartą przez operatora Stripe,</li>
                <li>tradycyjny przelew na rachunek sklepu (dane przelewu pojawiają się po złożeniu zamówienia),</li>
                <li>płatność przy odbiorze (za pobraniem), tylko przy dostawie kurierem, z dopłatą
                    {{ number_format(config('shop.cod_fee') / 100, 2, ',', ' ') }} zł.</li>
            </ul>

            <h2 class="subheading mt-6">§ 5. Dostawa</h2>
            <ul>
                @foreach (\App\Enums\DeliveryMethod::cases() as $method)
                    <li>{{ $method->label() }}:
                        {{ $method->cost() > 0 ? number_format($method->cost() / 100, 2, ',', ' ') . ' zł' : 'bezpłatnie' }}
                    </li>
                @endforeach
            </ul>
            <p>Koszt dostawy jest doliczany do zamówienia i widoczny w podsumowaniu przed jego złożeniem. Płatność za
                pobraniem nie jest dostępna przy dostawie do paczkomatu.</p>

            <h2 class="subheading mt-6">§ 6. Odstąpienie od umowy</h2>
            <p>Konsument może odstąpić od umowy zawartej na odległość w terminie 14 dni od otrzymania towaru, bez podawania
                przyczyny, wysyłając oświadczenie na adres e-mail sklepu.</p>
            <p>Prawo odstąpienia nie przysługuje w odniesieniu do produktów ulegających szybkiemu zepsuciu lub o krótkim
                terminie przydatności oraz dostarczanych w zapieczętowanym opakowaniu, których po otwarciu nie można
                zwrócić ze względów higienicznych (art. 38 ust. 1 pkt 4 i 5 ustawy o prawach konsumenta). Dotyczy to
                w szczególności porcjowanych serów.</p>

            <h2 class="subheading mt-6">§ 7. Reklamacje</h2>
            <p>Sklep odpowiada za zgodność towaru z umową. Reklamację można złożyć na adres e-mail sklepu, opisując
                problem i podając numer zamówienia. Sklep odpowiada na reklamację w terminie 14 dni.</p>

            <h2 class="subheading mt-6">§ 8. Postanowienia końcowe</h2>
            <p>Zasady przetwarzania danych osobowych opisuje <a href="{{ route('privacy') }}">polityka prywatności</a>.
                W sprawach nieuregulowanych stosuje się przepisy prawa polskiego, w szczególności Kodeksu cywilnego
                i ustawy o prawach konsumenta.</p>
        </div>
    </div>
@endsection
