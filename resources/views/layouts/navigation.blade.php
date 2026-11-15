<nav class="navbar">
    <div class="navbar__inner">
        <div class="navbar__left">
            <a href="{{ route('home') }}" class="navbar__brand">Gouda &amp; Spółka 🧀</a>
            <div class="navbar__links">
                <a href="{{ route('shop.index') }}" class="navbar__link">Sklep</a>
                <a href="{{ route('cart.show') }}" class="navbar__link">Koszyk 🛒</a>
            </div>
        </div>

        <div class="navbar__right">
            @auth
                <div class="usermenu" x-data="{ open: false }">
                    <button class="usermenu__trigger" @click="open = ! open">
                        {{ Auth::user()->name }} ▾
                    </button>
                    <div class="usermenu__dropdown" x-show="open" @click.outside="open = false" x-cloak>
                        @if (Auth::user()->role->slug === 'admin')
                            <a href="{{ route('admin.dashboard') }}" class="usermenu__item">Panel Admina 🧀</a>
                        @endif
                        <a href="{{ route('orders.index') }}" class="usermenu__item">Moje zamówienia</a>
                        <a href="{{ route('profile.edit') }}" class="usermenu__item">{{ __('Profile') }}</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="usermenu__item">{{ __('Log Out') }}</button>
                        </form>
                    </div>
                </div>
            @endauth

            @guest
                <a href="{{ route('login') }}" class="navbar__link">{{ __('Log in') }}</a>
                <a href="{{ route('register') }}" class="btn btn--yellow">{{ __('Register') }}</a>
            @endguest
        </div>
    </div>
</nav>
