# Gouda & Spółka 🧀: sklep internetowy z serami

Projekt inżynierski: sklep internetowy z serami sprzedawanymi na wagę (porcje po 100 g), zbudowany w Laravel 12.
Sklep jest **demonstracyjny**: zamówienia nie są realizowane, a płatności online działają w trybie testowym Stripe.

## Funkcje

**Klient**
- katalog z filtrami (kategoria, cena od/do, wyszukiwanie), sortowaniem i paginacją,
- koszyk dla gości i zalogowanych; koszyk gościa przechodzi na konto po zalogowaniu,
- kody rabatowe (procentowe i kwotowe, z progiem, limitem użyć i datą ważności),
- trzyetapowa kasa: adres (opcjonalnie dane do faktury na firmę) → dostawa i płatność → podsumowanie,
- dostawa: kurier, paczkomat InPost (lista punktów), odbiór osobisty; płatność: online (Stripe), przelew, za pobraniem,
- mail z potwierdzeniem zamówienia oraz podpisany link do śledzenia zamówienia (także dla gości),
- konto: rejestracja e-mail/hasło lub logowanie przez Google, zapisane adresy, historia zamówień,
- usunięcie konta z anonimizacją danych w zamówieniach (RODO),
- regulamin, polityka prywatności i wymagana zgoda przed złożeniem zamówienia.

**Administrator**
- pulpit ze statystykami (sprzedaż dziś, zamówienia, oczekujące, niski stan magazynowy),
- produkty: dodawanie, edycja, zdjęcia, usuwanie (miękkie, historia zamówień zostaje),
- zamówienia: podgląd, zmiana statusu realizacji i płatności; anulowanie zwraca towar na stan,
- kody rabatowe: dodawanie, edycja, usuwanie.

## Technologie

Laravel 12 (PHP 8.2), MySQL, Blade + Alpine.js, własny CSS budowany przez Vite, Laravel Breeze (uwierzytelnianie),
Laravel Socialite (Google), stripe-php (Stripe Checkout + webhook), PHPUnit.

## Wymagania

- PHP 8.2+ z rozszerzeniami `pdo_mysql`, `mbstring`, `openssl`, `fileinfo` (np. z pakietu XAMPP),
- Composer 2,
- MySQL 8 lub MariaDB,
- Node.js 18+ i npm,
- opcjonalnie: [Stripe CLI](https://docs.stripe.com/stripe-cli) (płatności online lokalnie) i projekt w Google Cloud (logowanie przez Google).

## Instalacja

```bash
git clone <adres-repozytorium> gouda
cd gouda

composer install
npm install

cp .env.example .env          # Windows (PowerShell): copy .env.example .env
php artisan key:generate
```

W pliku `.env` ustaw dane bazy (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`), adres aplikacji
`APP_URL=http://127.0.0.1:8000` oraz adres nadawcy maili `MAIL_FROM_ADDRESS`. Utwórz pustą bazę (domyślnie
`cheese_shop`, kodowanie `utf8mb4`), a potem:

```bash
php artisan migrate --seed    # tabele + kategorie, ~40 serów, role i konto administratora
php artisan storage:link      # publiczny dostęp do zdjęć produktów
npm run build                 # style i skrypty

php artisan serve             # http://127.0.0.1:8000
```

Przy pracy nad stylami zamiast `npm run build` można uruchomić `npm run dev` (podgląd zmian na żywo).

### Konto administratora

| E-mail | Hasło |
|---|---|
| `admin@admin.admin` | `zaq1@WSX` |

Panel jest dostępny z menu użytkownika („Panel Admina”) albo pod adresem `/admin`.
**Przed jakimkolwiek wdrożeniem zmień to hasło** (Profil → Zmiana hasła).

## Płatności online (Stripe, tryb testowy)

1. Załóż konto na [stripe.com](https://stripe.com) i z [kluczy testowych](https://dashboard.stripe.com/test/apikeys)
   wpisz do `.env`: `STRIPE_KEY` (`pk_test_...`) i `STRIPE_SECRET` (`sk_test_...`).
2. Zainstaluj Stripe CLI, zaloguj się (`stripe login`) i w osobnym terminalu uruchom:
   ```bash
   stripe listen --forward-to localhost:8000/stripe/webhook
   ```
3. Skopiuj wyświetlony klucz `whsec_...` do `STRIPE_WEBHOOK_SECRET` w `.env` i wykonaj `php artisan config:clear`.
4. W kasie wybierz „Płatność online” i zapłać testową kartą `4242 4242 4242 4242` (dowolna przyszła data, dowolny CVC).

Status „Opłacone” ustawia **webhook**: bez uruchomionego `stripe listen` zamówienie zostanie „Nieopłacone”.
Przelew i płatność za pobraniem działają bez Stripe; status płatności zmienia wtedy administrator.

## Logowanie przez Google (opcjonalnie)

1. W [Google Cloud Console](https://console.cloud.google.com/apis/credentials) utwórz identyfikator klienta OAuth
   (typ „Aplikacja internetowa”) z adresem przekierowania `http://127.0.0.1:8000/auth/google/callback`.
2. Wpisz `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET` i `GOOGLE_REDIRECT_URI` do `.env`.

Bez tej konfiguracji logowanie przez Google nie zadziała (Google pokaże błąd konfiguracji klienta); reszta sklepu,
w tym rejestracja e-mailem, działa normalnie.

## Maile

Domyślnie `MAIL_MAILER=log`: maile (potwierdzenie zamówienia, reset hasła) trafiają do `storage/logs/laravel.log`.
Do prawdziwej wysyłki ustaw serwer SMTP (`MAIL_MAILER=smtp`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`).

## Konfiguracja sklepu

Ustawienia w `config/shop.php` (nadpisywane w `.env`):

| Zmienna | Znaczenie |
|---|---|
| `SHOP_BANK_ACCOUNT` | numer rachunku pokazywany przy płatności przelewem |
| `SHOP_BANK_RECIPIENT` | odbiorca przelewu |
| `SHOP_COD_FEE` | dopłata za pobranie w groszach (domyślnie 500 = 5,00 zł) |

Koszty dostawy są zdefiniowane w `app/Enums/DeliveryMethod.php`, a lista paczkomatów w `config/shop.php`.

## Testy

```bash
php artisan test
```

Testy działają na osobnej bazie SQLite w pamięci (ustawienia w `phpunit.xml`), więc nie zmieniają danych w MySQL.

## Struktura projektu

| Katalog | Zawartość |
|---|---|
| `app/Http/Controllers` | kontrolery sklepu, koszyka, kasy i konta; `Admin/`: panel administratora |
| `app/Services` | logika biznesowa: `CartService`, `OrderService`, `PaymentService`, `CatalogService` |
| `app/Enums` | statusy zamówienia i płatności, metody dostawy i płatności |
| `app/Models` | modele Eloquent (`Product`, `Order`, `CartItem`, `DiscountCode`…) |
| `app/Listeners`, `app/Mail` | scalanie koszyka po zalogowaniu, mail z potwierdzeniem zamówienia |
| `resources/views` | widoki Blade; `admin/`: panel, `checkout/`: kasa, `pages/`: regulamin i polityka prywatności |
| `routes/web.php` | wszystkie trasy aplikacji |

## Przed wdrożeniem na serwer

- `APP_ENV=production` i **`APP_DEBUG=false`** (inaczej błędy pokazują kod i konfigurację),
- własne hasło administratora i prawdziwy `MAIL_FROM_ADDRESS`,
- HTTPS oraz webhook skonfigurowany w panelu Stripe zamiast `stripe listen`,
- prawdziwy serwer pocztowy; przy `QUEUE_CONNECTION=database` stały proces `php artisan queue:work`,
- `composer install --no-dev --optimize-autoloader`, `npm run build`, `php artisan optimize`.

## Ograniczenia (świadome uproszczenia)

- lista paczkomatów jest stała (bez mapy InPost, która wymaga konta firmowego),
- zwroty i reklamacje nie są obsługiwane w aplikacji; zwrot płatności online wykonuje się w panelu Stripe,
- faktury nie są generowane, sklep zapisuje jedynie dane do faktury.
