@extends('layouts.app')

@section('content')
    <div class="container-narrow">
        <div class="card">
            <h1 class="section-title">Polityka prywatności</h1>
            <p class="muted">Obowiązuje od 23 listopada 2026 r.</p>

            <div class="summary-box mt-4">
                {{ config('app.name') }} jest sklepem <strong>demonstracyjnym</strong> (projekt inżynierski). Poniższe
                zasady opisują, jakie dane zbiera aplikacja i jak z nimi postępuje.
            </div>

            <h2 class="subheading mt-6">1. Administrator danych</h2>
            <p>Administratorem danych osobowych jest {{ config('app.name') }}. W sprawach danych osobowych można pisać na
                adres <strong>{{ config('mail.from.address') }}</strong>.</p>

            <h2 class="subheading mt-6">2. Jakie dane zbieramy</h2>
            <ul>
                <li><strong>konto:</strong> imię, adres e-mail, hasło (przechowywane wyłącznie w postaci skrótu
                    kryptograficznego) albo identyfikator konta Google przy logowaniu przez Google,</li>
                <li><strong>zamówienia:</strong> imię i nazwisko, adres dostawy, telefon, e-mail, wybrany paczkomat,
                    a przy fakturze nazwa firmy, NIP i adres firmy,</li>
                <li><strong>zapisane adresy</strong>, jeśli klient zdecyduje się je zachować w profilu.</li>
            </ul>
            <p>Danych karty płatniczej sklep nie widzi i nie przechowuje. Płatność online odbywa się na stronie operatora
                Stripe.</p>

            <h2 class="subheading mt-6">3. Cele i podstawy przetwarzania</h2>
            <ul>
                <li>zawarcie i wykonanie umowy sprzedaży oraz prowadzenie konta (art. 6 ust. 1 lit. b RODO),</li>
                <li>wypełnienie obowiązków prawnych, np. podatkowych i rachunkowych (art. 6 ust. 1 lit. c RODO),</li>
                <li>zapewnienie bezpieczeństwa serwisu oraz ustalenie i dochodzenie roszczeń (art. 6 ust. 1 lit. f RODO).
                </li>
            </ul>

            <h2 class="subheading mt-6">4. Odbiorcy danych</h2>
            <p>Dane mogą być przekazywane podmiotom, które pomagają w realizacji zamówienia: operatorowi płatności
                (Stripe), dostawcy logowania (Google, tylko przy wyborze tej metody) oraz firmie kurierskiej lub InPost
                (dane potrzebne do doręczenia przesyłki).</p>

            <h2 class="subheading mt-6">5. Jak długo przechowujemy dane</h2>
            <p>Dane konta przechowujemy do czasu jego usunięcia. Po usunięciu konta dane osobowe w zamówieniach są
                anonimizowane. Zostają tylko informacje potrzebne do rozliczeń i statystyk sprzedaży (produkty, kwoty,
                daty). Dokumenty księgowe przechowuje się przez okres wymagany przepisami podatkowymi (5 lat).</p>

            <h2 class="subheading mt-6">6. Twoje prawa</h2>
            <p>Masz prawo do dostępu do swoich danych, ich sprostowania, usunięcia, ograniczenia przetwarzania,
                przenoszenia oraz wniesienia sprzeciwu. Konto wraz z danymi możesz usunąć samodzielnie w ustawieniach
                profilu. Przysługuje Ci też prawo wniesienia skargi do Prezesa Urzędu Ochrony Danych Osobowych.</p>

            <h2 class="subheading mt-6">7. Pliki cookies</h2>
            <p>Sklep używa wyłącznie niezbędnych plików cookies technicznych:</p>
            <ul>
                <li>ciasteczka sesji, dzięki któremu działa koszyk i logowanie,</li>
                <li>tokenu zabezpieczającego formularze przed atakami CSRF,</li>
                <li>ciasteczka „Zapamiętaj mnie”, tylko jeśli klient zaznaczy tę opcję przy logowaniu.</li>
            </ul>
            <p>Sklep nie używa ciasteczek reklamowych ani analitycznych. Cookies można usunąć w ustawieniach przeglądarki,
                ale bez nich koszyk i logowanie nie będą działać.</p>

            <p class="mt-6">Zobacz też: <a href="{{ route('terms') }}">regulamin sklepu</a>.</p>
        </div>
    </div>
@endsection
