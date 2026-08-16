<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center font-bold text-yellow-600">
                    <a href="{{ route('home') }}">Gouda & Spółka 🧀</a>
                </div>
                <div class="hidden sm:flex sm:items-center sm:ms-10 space-x-6">
                    <a href="{{ route('home') }}"
                        class="text-sm font-medium text-gray-600 hover:text-yellow-600">Sklep</a>
                    <a href="{{ route('cart.show') }}"
                        class="text-sm font-medium text-gray-600 hover:text-yellow-600">Koszyk 🛒</a>
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button
                                class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition">
                                <div>{{ Auth::user()->name }}</div>
                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            @if (Auth::user()->role->slug === 'admin')
                                <x-dropdown-link :href="route('admin.dashboard')">Panel Admina 🧀</x-dropdown-link>
                            @endif
                            <x-dropdown-link :href="route('profile.edit')">{{ __('Profile') }}</x-dropdown-link>
                            <x-dropdown-link :href="route('orders.index')">Moje zamówienia</x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @endauth

                @guest
                    <a href="{{ route('login') }}" class="text-sm text-gray-700 hover:text-yellow-600">Zaloguj się</a>
                    <a href="{{ route('register') }}"
                        class="ml-4 px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm rounded-md">Zarejestruj</a>
                @endguest
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('home')">Sklep</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('cart.show')">Koszyk 🛒</x-responsive-nav-link>
        </div>
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                @auth
                    <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                @endauth
                @guest
                    <div class="font-medium text-base text-gray-800">Witaj, Gościu! 🧀</div>
                    <div class="font-medium text-sm text-gray-500">Zaloguj się, aby złożyć zamówienie.</div>
                @endguest
            </div>

            <div class="mt-3 space-y-1">
                @auth
                    @if (Auth::user()->role->slug === 'admin')
                        <x-responsive-nav-link :href="route('admin.dashboard')">Panel Admina 🧀</x-responsive-nav-link>
                    @endif
                    <x-responsive-nav-link :href="route('orders.index')">Moje zamówienia</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('profile.edit')">{{ __('Profile') }}</x-responsive-nav-link>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                @endauth
                @guest
                    <x-responsive-nav-link :href="route('login')">Zaloguj się</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('register')">Zarejestruj</x-responsive-nav-link>
                @endguest
            </div>
        </div>
    </div>
</nav>
