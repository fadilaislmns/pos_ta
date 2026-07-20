@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- HEADER -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold">Daftar Penjualan</h4>
            @if (auth()->user()->role->role_name == 'cashier')
                <a href="{{ route('sales.create') }}" class="btn btn-dark">
                    + Penjualan Baru
                </a>
            @endif
        </div>

        <!-- FILTER -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">

                <div class="row g-3 align-items-end">

                    <div class="col-md-3">
                        <label class="fw-bold">Cari</label>
                        <input type="text" id="search" class="form-control" placeholder="Invoice / pelanggan">
                    </div>
                    @if (auth()->user()->role->role_name == 'admin')
                        <div class="col-md-2">
                            <label class="fw-bold">Kasir</label>
                            <select id="user_id" class="form-control">
                                <option value="">Semua Kasir</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="col-md-2">
                        <label class="fw-bold">Dari Tanggal</label>
                        <input type="date" id="start_date" class="form-control">
                    </div>

                    <div class="col-md-2">
                        <label class="fw-bold">Sampai Tanggal</label>
                        <input type="date" id="end_date" class="form-control">
                    </div>

                    <div class="col-md-2">
                        <label class="fw-bold">Metode</label>
                        <select id="payment_type" class="form-control">
                            <option value="">Semua</option>
                            <option value="cash">Cash</option>
                            <option value="credit">Credit</option>
                        </select>
                    </div>

                    <div class="col-md-1">
                        <button class="btn btn-secondary w-100" onclick="resetFilter()">
                            <i class="fas fa-redo"></i>
                        </button>
                    </div>

                </div>

            </div>
        </div>

        <!-- TABLE -->
        <div class="card">

            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>No Invoice</th>
                        <th>Kasir</th>
                        <th>Pelanggan</th>
                        <th>Total</th>
                        <th>Metode</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody id="salesTable">
                    <tr>
                        <td colspan="7" class="text-center">Loading...</td>
                    </tr>
                </tbody>
            </table>

            <!-- FOOTER (LIMIT + PAGINATION) -->
            <div class="d-flex justify-content-between align-items-center p-3">
                <div class="d-flex align-items-center">
                    <span class="mr-2">Show</span>
                    <select class="form-control mr-2" style="width: 80px;" onchange="changeLimit(this.value)">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                    <span id="totalData" class="text-muted small"></span>
                </div>

                <nav>
                    <ul class="pagination mb-0" id="pagination">
                    </ul>
                </nav>
            </div>

        </div>

        <!-- SUMMARY -->
        <div class="row mt-3">

            <!-- TOTAL TRANSAKSI -->
            <div class="col-md-3">
                <div class="card p-3 shadow-sm border-0 d-flex flex-row align-items-center">
                    <div class="bg-primary text-white rounded p-3 mr-3">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div>
                        <small>Total Transaksi</small>
                        <h5 id="totalTransaksi">0</h5>
                    </div>
                </div>
            </div>

            <!-- TOTAL PENJUALAN -->
            <div class="col-md-3">
                <div class="card p-3 shadow-sm border-0 d-flex flex-row align-items-center">
                    <div class="bg-success text-white rounded p-3 mr-3">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div>
                        <small>Total Penjualan</small>
                        <h5 id="totalPenjualan">Rp 0</h5>
                    </div>
                </div>
            </div>

            <!-- RATA-RATA -->
            <div class="col-md-3">
                <div class="card p-3 shadow-sm border-0 d-flex flex-row align-items-center">
                    <div class="bg-warning text-white rounded p-3 mr-3">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div>
                        <small>Rata-rata Transaksi</small>
                        <h5 id="avgTransaksi">Rp 0</h5>
                    </div>
                </div>
            </div>

            <!-- HARI INI -->
            <div class="col-md-3">
                <div class="card p-3 shadow-sm border-0 d-flex flex-row align-items-center">
                    <div class="bg-purple text-white rounded p-3 mr-3">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div>
                        <small>Transaksi Hari Ini</small>
                        <h5 id="todayTransaksi">0</h5>
                    </div>
                </div>
            </div>

        </div>

    </div>
@endsection


@push('scripts')
    <script>
        let currentPage = 1;
        let limit = 10;

        function loadData(page = 1) {

            currentPage = page;

            let search = document.getElementById('search').value;
            let start_date = document.getElementById('start_date').value;
            let end_date = document.getElementById('end_date').value;
            let userElement = document.getElementById('user_id');
            let user_id = userElement ? userElement.value : '';
            let payment_type = document.getElementById('payment_type').value;
            let params = new URLSearchParams();

            if (search) params.append('search', search);
            if (start_date) params.append('start_date', start_date);
            if (end_date) params.append('end_date', end_date);
            if (user_id) params.append('user_id', user_id);
            if (payment_type) params.append('payment_type', payment_type);

            params.append('page', page);
            params.append('limit', limit);

            let url = `/sales-data?${params.toString()}`;

            fetch(url)
                .then(res => res.json())
                .then(res => {

                    let data = res.data;
                    let html = '';

                    if (data.length === 0) {
                        html = `<tr><td colspan="7" class="text-center">Data kosong</td></tr>`;
                    } else {
                        data.forEach((item, index) => {
                            html += `
                        <tr>
                            <td>${(res.current_page - 1) * limit + index + 1}</td>
                            <td>${item.sale_date}</td>
                            <td>${item.invoice_no}</td>
                            <td>${item.user ? item.user.name : '-'}</td>
                            <td>${item.customer ? item.customer.customer_name : '-'}</td>
                            <td>Rp ${parseInt(item.total_amount).toLocaleString('id-ID')}</td>
                            <td>${item.payment_type}</td>
                            <td>
                                <a href="/sales/${item.id}" class="btn btn-sm btn-light">
                                    <i class="fas fa-eye text-info"></i>
                                </a>
                                @if (auth()->user()->role->role_name == 'admin')
                                    <a href="/sales/${item.id}/edit"
                                        class="btn btn-light btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    `;
                        });
                    }

                    document.getElementById('salesTable').innerHTML = html;
                    document.getElementById('totalData').innerText = `from ${res.total} data`;
                    let userElement = document.getElementById('user_id');
                    if (userElement) {
                        userElement.addEventListener(
                            'change',
                            () => loadData(1)
                        );
                    }

                    renderPagination(res);

                    // ===== SUMMARY =====
                    let totalTransaksi = res.total;
                    let totalPenjualan = 0;
                    let today = new Date().toISOString().slice(0, 10);
                    let todayCount = 0;

                    res.data.forEach(item => {
                        totalPenjualan += parseFloat(item.total_amount);

                        if (item.sale_date === today) {
                            todayCount++;
                        }
                    });

                    let avg = totalTransaksi > 0 ? totalPenjualan / totalTransaksi : 0;

                    document.getElementById('totalTransaksi').innerText = totalTransaksi;
                    document.getElementById('totalPenjualan').innerText =
                        'Rp ' + totalPenjualan.toLocaleString('id-ID');
                    document.getElementById('avgTransaksi').innerText =
                        'Rp ' + Math.round(avg).toLocaleString('id-ID');
                    document.getElementById('todayTransaksi').innerText = todayCount;

                })
                .catch(err => console.error(err));
        }


        // PAGINATION
        function renderPagination(res) {
            let html = '';

            // Tombol Previous
            html += `
                <li class="page-item ${res.current_page === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="#" onclick="loadData(${res.current_page - 1})">Prev</a>
                </li>
            `;

            // Angka Halaman
            for (let i = 1; i <= res.last_page; i++) {
                html += `
                <li class="page-item ${i === res.current_page ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="loadData(${i})">${i}</a>
                </li>
            `;
            }

            // Tombol Next
            html += `
                <li class="page-item ${res.current_page === res.last_page ? 'disabled' : ''}">
                    <a class="page-link" href="#" onclick="loadData(${res.current_page + 1})">Next</a>
                </li>
            `;

            document.getElementById('pagination').innerHTML = html;
        }


        // RESET
        function resetFilter() {
            document.getElementById('search').value = '';
            document.getElementById('start_date').value = '';
            document.getElementById('end_date').value = '';
            document.getElementById('payment_type').value = '';

            loadData();
        }


        // LIMIT
        function changeLimit(val) {
            limit = val;
            loadData(1);
        }


        // REALTIME SEARCH
        let delayTimer;

        document.getElementById('search').addEventListener('keyup', function() {
            clearTimeout(delayTimer);
            delayTimer = setTimeout(() => loadData(), 500);
        });


        // AUTO FILTER
        document.getElementById('start_date').addEventListener('change', () => loadData(1));
        document.getElementById('end_date').addEventListener('change', () => loadData(1));
        document.getElementById('payment_type').addEventListener('change', () => loadData(1));


        // LOAD AWAL
        document.addEventListener('DOMContentLoaded', function() {
            loadData();
        });
    </script>
@endpush
