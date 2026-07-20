<aside class="main-sidebar elevation-4">

    <!-- LOGO -->
    <a href="/dashboard" class="brand-link text-center">
        <i class="fas fa-cash-register mr-2"></i>
        <span>Amila POS</span>
    </a>

    @php
        $role = auth()->user()->role->role_name;
    @endphp

    <div class="sidebar">
        <nav>
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" data-accordion="false">

                {{-- DASHBOARD --}}
                <li class="nav-header">MAIN MENU</li>

                <li class="nav-item">
                    <a href="{{ route('dashboard') }}"
                        class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}">
                        @if ($role == 'super_admin')
                            <i class="fas fa-tachograph-digital nav-icon"></i>
                        @elseif($role == 'admin')
                            <i class="fas fa-tachograph-digital nav-icon"></i>
                        @elseif($role == 'cashier')
                            <i class="fas fa-tachograph-digital nav-icon"></i>
                        @endif
                        <p>Dashboard</p>
                    </a>
                </li>

                {{-- SUPER ADMIN --}}
                @if ($role == 'super_admin')
                    <li class="nav-header">SYSTEM</li>
                    <li class="nav-item">
                        <a href="{{ route('users.index') }}"
                            class="nav-link {{ request()->is('users*') ? 'active' : '' }}">
                            <i class="fas fa-users-cog nav-icon"></i>
                            <p>Manajemen User</p>
                        </a>
                    </li>
                @endif

                {{-- ADMIN --}}
                @if ($role == 'admin')
                    <!-- MASTER -->
                    <li class="nav-header">MASTER</li>

                    <li
                        class="nav-item has-treeview
                        {{ request()->is('categories*') ||
                        request()->is('products*') ||
                        request()->is('suppliers*') ||
                        request()->is('customers*')
                            ? 'menu-open'
                            : '' }}">

                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-database"></i>
                            <p>
                                Master
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>

                        <ul class="nav nav-treeview">
                            <!-- CATEGORY -->
                            <li class="nav-item">
                                <a href="{{ route('categories.index') }}"
                                    class="nav-link {{ request()->is('categories*') ? 'active' : '' }}">
                                    <i class="fas fa-tags nav-icon"></i>
                                    <p>Kategori</p>
                                </a>
                            </li>

                            <!-- PRODUCT -->
                            <li class="nav-item">
                                <a href="{{ route('products.index') }}"
                                    class="nav-link {{ request()->is('products*') ? 'active' : '' }}">
                                    <i class="fas fa-box nav-icon"></i>
                                    <p>Produk</p>
                                </a>
                            </li>

                            <!-- SUPPLIER -->
                            <li class="nav-item">
                                <a href="{{ route('suppliers.index') }}"
                                    class="nav-link {{ request()->is('suppliers*') ? 'active' : '' }}">
                                    <i class="fas fa-truck nav-icon"></i>
                                    <p>Supplier</p>
                                </a>
                            </li>
                             <!-- DISKON -->
                            <li class="nav-item">
                                <a href="{{ route('discounts.index') }}"
                                    class="nav-link {{ request()->is('discount*') ? 'active' : '' }}">
                                    <i class="fas fa-percent nav-icon"></i>
                                    <p>Diskon</p>
                                </a>
                            </li>

                            <!-- CUSTOMER -->
                            {{-- <li class="nav-item">
                                <a href="{{ route('customers.index') }}"
                                    class="nav-link {{ request()->is('customers*') ? 'active' : '' }}">
                                    <i class="fas fa-users nav-icon"></i>
                                    <p>Pelanggan</p>
                                </a>
                            </li> --}}
                        </ul>
                    </li>

                    <!-- TRANSAKSI -->
                    <li class="nav-header">TRANSAKSI</li>
                    <li
                        class="nav-item has-treeview
                        {{ request()->is('sales*') || request()->is('purchases*') || request()->is('customer-debts*')
                            ? 'menu-open'
                            : '' }}">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-shopping-cart"></i>
                            <p>
                                Transaksi
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>

                        <ul class="nav nav-treeview">
                            <!-- SALES -->
                            <li class="nav-item">
                                <a href="{{ route('sales.index') }}"
                                    class="nav-link {{ request()->is('sales*') ? 'active' : '' }}">
                                    <i class="fas fa-cash-register nav-icon"></i>
                                    <p>Penjualan</p>
                                </a>
                            </li>

                            <!-- PURCHASE -->
                            <li class="nav-item">
                                <a href="{{ route('purchases.index') }}"
                                    class="nav-link {{ request()->is('purchases*') ? 'active' : '' }}">
                                    <i class="fas fa-file-invoice nav-icon"></i>
                                    <p>Pembelian</p>
                                </a>

                            </li>

                            <!-- PIUTANG -->
                            <li class="nav-item">
                                <a href="{{ route('customer-debts.index') }}"
                                    class="nav-link {{ request()->is('customer-debts*') ? 'active' : '' }}">
                                    <i class="fas fa-wallet nav-icon"></i>
                                    <p>Piutang Pelanggan</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- INVENTORY -->
                    <li class="nav-header">INVENTORY</li>
                    <li
                        class="nav-item has-treeview
                        {{ request()->is('stocks*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-warehouse"></i>
                            <p>
                                Inventory
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>

                        <ul class="nav nav-treeview">
                            <!-- STOCK -->
                            <li class="nav-item">
                                <a href="{{ route('stocks.index') }}"
                                    class="nav-link {{ request()->is('stocks') ? 'active' : '' }}">
                                    <i class="fas fa-boxes nav-icon"></i>
                                    <p>Stock</p>
                                </a>
                            </li>

                            <!-- STOCK MOVEMENT -->
                            <li class="nav-item">
                                <a href="{{ route('stocks.movements') }}"
                                    class="nav-link {{ request()->is('stocks/movements*') ? 'active' : '' }}">
                                    <i class="fas fa-exchange-alt nav-icon"></i>
                                    <p>Stock Movement</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <!-- LAPORAN -->
                    <li class="nav-header">LAPORAN</li>
                    <li
                        class="nav-item has-treeview
                        {{ request()->is('reports*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-chart-pie"></i>
                            <p>
                                Laporan
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <!-- LAPORAN PENJUALAN -->
                            <li class="nav-item">
                                <a href="{{ route('reports.sales') }}"
                                    class="nav-link {{ request()->is('reports/sales*') ? 'active' : '' }}">
                                    <i class="far fa-file nav-icon"></i>
                                    <p>Laporan Penjualan</p>
                                </a>
                            </li>

                            <!-- LAPORAN PEMBELIAN -->
                            <li class="nav-item">
                                <a href="{{ route('reports.purchases') }}"
                                    class="nav-link {{ request()->is('reports/purchases*') ? 'active' : '' }}">
                                    <i class="fas fa-shopping-basket nav-icon"></i>
                                    <p>Laporan Pembelian</p>
                                </a>
                            </li>

                            <!-- ANALISIS PROFIT -->
                            <li class="nav-item">
                                <a href="{{ route('reports.profit') }}"
                                    class="nav-link {{ request()->is('reports/profit*') ? 'active' : '' }}">
                                    <i class="fas fa-chart-line nav-icon"></i>
                                    <p>Analisis Profit</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- PERAMALAN -->
                    <li class="nav-header">ANALISIS & PERAMALAN</li>
                    <li class="nav-item has-treeview {{ request()->is('forecast*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-chart-line"></i>
                            <p> Analisis & Peramalan
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('forecast.index') }}"
                                    class="nav-link {{ request()->is('forecast/sales*') ? 'active' : '' }}">
                                    <i class="fas fa-chart-area nav-icon"></i>
                                    <p>Forecast Kebutuhan Barang</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('forecast.recommendation') }}"
                                    class="nav-link {{ request()->is('forecast/recommendation*') ? 'active' : '' }}">
                                    <i class="fas fa-shopping-cart nav-icon"></i>
                                    <p>Rekomendasi Pembelian</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('forecast.simulation') }}"
                                    class="nav-link {{ request()->is('forecast/simulation*') ? 'active' : '' }}">
                                    <i class="fas fa-coins nav-icon"></i>
                                    <p>Analisis Profitabilitas</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                {{-- CASHIER --}}
                @if ($role == 'cashier')
                    <!-- TRANSAKSI -->
                    <li class="nav-header">TRANSAKSI</li>
                    <!-- SALES -->
                    <li class="nav-item">
                        <a href="{{ route('sales.index') }}"
                            class="nav-link {{ request()->is('sales*') ? 'active' : '' }}">
                            <i class="fas fa-shopping-cart nav-icon"></i>
                            <p>Penjualan</p>
                        </a>
                    </li>

                    <!-- PIUTANG -->
                    <li class="nav-item">
                        <a href="{{ route('customer-debts.index') }}"
                            class="nav-link {{ request()->is('customer-debts*') ? 'active' : '' }}">
                            <i class="fas fa-wallet nav-icon"></i>
                            <p>Piutang Pelanggan</p>
                        </a>
                    </li>

                    <!-- INVENTORY -->
                    <li class="nav-header">INVENTORY</li>
                    <!-- STOCK -->
                    <li class="nav-item">
                        <a href="{{ route('stocks.index') }}"
                            class="nav-link {{ request()->is('stocks') ? 'active' : '' }}">
                            <i class="fas fa-boxes nav-icon"></i>
                            <p>Stock</p>
                        </a>
                    </li>
                    <!-- LAPORAN PENJUALAN -->
                    <li class="nav-header">LAPORAN</li>
                    <li class="nav-item">
                        <a href="{{ route('reports.sales') }}"
                            class="nav-link {{ request()->is('reports/sales*') ? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Laporan Penjualan</p>
                        </a>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
</aside>
