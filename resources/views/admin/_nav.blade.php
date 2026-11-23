<nav class="admin-panel__actions mb-6">
    <a href="{{ route('admin.dashboard') }}"
        class="btn {{ request()->routeIs('admin.dashboard') ? 'btn--dark' : 'btn--light' }}">Pulpit</a>
    <a href="{{ route('admin.products.index') }}"
        class="btn {{ request()->routeIs('admin.products.*') ? 'btn--dark' : 'btn--light' }}">Produkty</a>
    <a href="{{ route('admin.order.index') }}"
        class="btn {{ request()->routeIs('admin.order.*') ? 'btn--dark' : 'btn--light' }}">Zamówienia</a>
    <a href="{{ route('admin.discounts.index') }}"
        class="btn {{ request()->routeIs('admin.discounts.*') ? 'btn--dark' : 'btn--light' }}">Kody rabatowe</a>
</nav>
