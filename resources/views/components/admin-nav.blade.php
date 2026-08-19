@php
    $items = [
        ['route' => 'admin.dashboard', 'icon' => 'dashboard', 'label' => 'Dashboard'],
        ['route' => 'admin.orders.index', 'icon' => 'receipt_long', 'label' => 'Pesanan'],
        ['route' => 'admin.products.index', 'icon' => 'inventory_2', 'label' => 'Produk'],
        ['route' => 'admin.categories.index', 'icon' => 'sell', 'label' => 'Kategori'],
        ['route' => 'admin.variants.index', 'icon' => 'tune', 'label' => 'Varian'],
        ['route' => 'admin.ingredients.index', 'icon' => 'warehouse', 'label' => 'Bahan Baku'],
        ['route' => 'admin.recipes.index', 'icon' => 'menu_book', 'label' => 'Resep'],
        ['route' => 'admin.promos.index', 'icon' => 'percent', 'label' => 'Promo'],
        ['route' => 'admin.banners.index', 'icon' => 'image', 'label' => 'Banner'],
        ['route' => 'admin.employees.index', 'icon' => 'groups', 'label' => 'Karyawan'],
        ['route' => 'admin.outlets.index', 'icon' => 'storefront', 'label' => 'Outlet'],
        ['route' => 'admin.tables.index', 'icon' => 'table_restaurant', 'label' => 'Meja'],
        ['route' => 'admin.reports.index', 'icon' => 'bar_chart', 'label' => 'Laporan'],
        ['route' => 'admin.settings.index', 'icon' => 'settings', 'label' => 'Pengaturan'],
    ];
@endphp

<nav class="flex flex-1 flex-col gap-1 px-4">
    @foreach ($items as $item)
        @php $active = request()->routeIs($item['route'].'*'); @endphp
        <a href="{{ route($item['route']) }}"
           class="flex items-center gap-3 rounded-lg px-4 py-3 text-body-sm font-medium transition-all
                  {{ $active ? 'bg-primary text-on-primary' : 'text-on-surface-variant hover:bg-surface-container-low hover:text-on-surface' }}">
            <span class="material-symbols-outlined text-[20px]">{{ $item['icon'] }}</span>
            {{ $item['label'] }}
        </a>
    @endforeach
</nav>