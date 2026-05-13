<aside class="main-sidebar elevation-4">

    <!-- LOGO -->
    <a href="/dashboard" class="brand-link text-center">
        <i class="fas fa-cash-register mr-2"></i>
        <span>Amila POS</span>
    </a>

    <div class="sidebar">
        <nav>
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" data-accordion="false">

                <!-- MAIN -->
                <li class="nav-header">MAIN MENU</li>

                <li class="nav-item">
                    <a href="/dashboard" class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt nav-icon"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <!-- MANAGEMENT -->
                <li class="nav-header">MANAGEMENT</li>

                <li class="nav-item">
                    <a href="/categories" class="nav-link {{ request()->is('categories*') ? 'active' : '' }}">
                        <i class="far fa-folder nav-icon"></i>
                        <p>Kategori</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="/products" class="nav-link {{ request()->is('products*') ? 'active' : '' }}">
                        <i class="fas fa-box nav-icon"></i>
                        <p>Produk</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="/suppliers" class="nav-link {{ request()->is('suppliers*') ? 'active' : '' }}">
                        <i class="fas fa-truck nav-icon"></i>
                        <p>Pemasok</p>
                    </a>
                </li>

                <!-- TRANSAKSI -->
                <li class="nav-header">TRANSAKSI</li>

                <li class="nav-item has-treeview {{ request()->is('sales*') || request()->is('purchases*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-shopping-cart"></i>
                        <p>
                            Transaksi
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="/sales" class="nav-link {{ request()->is('sales*') ? 'active' : '' }}">
                                <i class="fas fa-cash-register nav-icon"></i>
                                <p>Penjualan</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="/purchases" class="nav-link {{ request()->is('purchases*') ? 'active' : '' }}">
                                <i class="fas fa-file-invoice nav-icon"></i>
                                <p>Pembelian</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- INVENTORY -->
                <li class="nav-header">INVENTORY</li>

                <li class="nav-item has-treeview {{ request()->is('stocks*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-warehouse"></i>
                        <p>
                            Inventory
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('stocks.index') }}" class="nav-link {{ request()->is('stocks') ? 'active' : '' }}">
                                <i class="fas fa-boxes nav-icon"></i>
                                <p>Stok</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('stocks.movements') }}" class="nav-link {{ request()->is('stocks/movements*') ? 'active' : '' }}">
                                <i class="fas fa-exchange-alt nav-icon"></i>
                                <p>Stok Movement</p>
                            </a>
                        </li>
                    </ul>
                </li>

            </ul>
        </nav>
    </div>
</aside>
