@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold">Piutang Pelanggan</h4>
    </div>
    <div class="d-flex justify-content-between align-items-center mb-3">

        <!-- SEARCH -->
        <div style="width:300px;">
            <input type="text" id="searchInput" class="form-control" placeholder="Cari customer...">
        </div>

        <!-- FILTER BUTTON -->
        <button class="btn btn-outline-dark mr-2" id="openFilter">
            <i class="fas fa-filter"></i>
            Filter
        </button>
    </div>

    <div class="card">
        <!-- TABLE -->
        <div class="card-body table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Customer</th>
                        <th>Total Hutang</th>
                        <th>Jatuh Tempo</th>
                        <th>Status</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($debts as $d)
                        <tr data-status="{{ $d->status }}">
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ \Carbon\Carbon::parse($d->sale->sale_date)->format('d/m/Y') }}</td>
                            <td>{{ $d->sale->customer->customer_name ?? '-' }}</td>
                            <td> Rp {{ number_format($d->amount, 0, ',', '.') }}</td>
                            <td>{{ \Carbon\Carbon::parse($d->due_date)->format('d/m/Y') }}</td>
                            <td>
                                @if ($d->status == 'active')
                                    <span class="badge badge-danger">Belum Lunas</span>
                                @else
                                    <span class="badge badge-success">Lunas</span>
                                @endif
                            </td>

                            <td class="d-flex gap-1">
                                <!-- DETAIL -->
                                <button class="btn btn-info btn-sm btn-light btn-detail" data-id="{{ $d->id }}">
                                    <i class="fas fa-eye text-info"></i>
                                </button>

                                @if ($d->status == 'active')
                                    <!-- LUNASI -->
                                    <button class="btn btn-success btn-light btn-pay" data-id="{{ $d->id }}"
                                        data-amount="{{ $d->amount }}">
                                        <i class="fas fa-money-bill-wave text-success"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty

                        <tr>
                            <td colspan="6" class="text-center">
                                Tidak ada data piutang
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="row align-items-center mt-4">

                <div class="col-md-6 text-muted small">
                    Menampilkan <strong>{{ $debts->firstItem() }}</strong> sampai
                    <strong>{{ $debts->lastItem() }}</strong> dari
                    <strong>{{ $debts->total() }}</strong> data
                </div>

                <div class="col-md-6 d-flex justify-content-end align-items-center">

                    <div class="mr-3">
                        <select id="perPageSelect" class="form-control form-control-sm"
                            style="width: 70px; border-radius: 8px;">
                            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        </select>
                    </div>

                    <div>
                        {{ $debts->appends(request()->query())->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FILTER PANEL -->
    <div class="filter-panel" id="filterPanel">

        <!-- HEADER -->
        <div class="filter-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Filter</h5>
            <button class="btn btn-sm btn-light" id="closeFilter">
                <i class="fas fa-times"></i>
            </button>

        </div>

        <!-- BODY -->
        <div class="filter-body">
            <!-- STATUS -->
            <div class="form-group">
                <label class="filter-label">Status</label>

                <div class="filter-radio-group">
                    <!-- SEMUA -->
                    <label class="filter-radio">
                        <input type="radio" name="status" value="" checked>
                        <span>Semua</span>
                    </label>

                    <!-- ACTIVE -->
                    <label class="filter-radio">
                        <input type="radio" name="status" value="active">
                        <span>Belum Lunas</span>
                    </label>

                    <!-- PAID -->
                    <label class="filter-radio">
                        <input type="radio" name="status" value="paid">
                        <span>Lunas</span>

                    </label>

                </div>

            </div>

            <!-- DATE FROM -->
            <div class="form-group">
                <label>Dari Tanggal</label>
                <input type="date" id="dateFrom" class="form-control">
            </div>

            <!-- DATE TO -->
            <div class="form-group">
                <label>Sampai Tanggal</label>
                <input type="date" id="dateTo" class="form-control">
            </div>
        </div>

        <!-- FOOTER -->
        <div class="filter-footer d-flex justify-content-between">
            <button class="btn btn-light" id="resetFilter">
                Reset
            </button>
            <button class="btn btn-success" id="applyFilter">
                Terapkan
            </button>
        </div>
    </div>

    <!-- MODAL DETAIL -->
    <div class="modal fade" id="modalDetail" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <!-- HEADER -->
                <div class="modal-header">
                    <h5 class="modal-title">
                        Detail Piutang
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <!-- BODY -->
                <div class="modal-body">
                    <!-- INFO -->
                    <div class="row mb-3">

                        <!-- INVOICE -->
                        <div class="col-md-4">
                            <small>Invoice</small>

                            <div>
                                <b id="detailInvoice"></b>
                            </div>
                        </div>

                        <!-- TANGGAL -->
                        <div class="col-md-4">
                            <small>Tanggal</small>

                            <div>
                                <b id="detailDate"></b>
                            </div>
                        </div>

                        <!-- STATUS -->
                        <div class="col-md-4">
                            <small>Status</small>

                            <div>
                                <b id="detailStatus"></b>
                            </div>
                        </div>

                        <!-- CUSTOMER -->
                        <div class="col-md-4 mt-3">
                            <small>Customer</small>
                            <div>
                                <b id="detailCustomer"></b>
                            </div>
                        </div>

                        <!-- NO HP -->
                        <div class="col-md-4 mt-3">
                            <small>No HP</small>
                            <div>
                                <b id="detailPhone"></b>
                            </div>
                        </div>
                        <div class="col-md-4 mt-3">
                            <small>Kasir</small>
                            <div>
                                <b id="detailCashier"></b>
                            </div>
                        </div>

                    </div>

                    <!-- RINGKASAN -->
                    <div class="row mb-4">
                        <!-- TOTAL -->
                        <div class="col-md-4">
                            <small class="text-muted">
                                Total
                            </small>
                            <div>
                                <b id="detailTotal"></b>
                            </div>
                        </div>

                        <!-- DIBAYAR -->
                        <div class="col-md-4">
                            <small class="text-muted">
                                Dibayar
                            </small>

                            <div>
                                <b class="text-primary" id="detailPaid"></b>
                            </div>
                        </div>

                        <!-- KEMBALIAN -->
                        <div class="col-md-4" id="changeWrapper">
                            <small class="text-muted">
                                Kembalian
                            </small>

                            <div>
                                <b class="text-success" id="detailChange"></b>
                            </div>
                        </div>

                        <!-- HUTANG -->
                        <div class="col-md-4" id="debtWrapper">
                            <small class="text-muted">
                                Sisa Hutang
                            </small>

                            <div>
                                <b class="text-danger" id="detailDebt"></b>
                            </div>

                        </div>

                    </div>
                    <!-- TABLE -->
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>Qty</th>
                                    <th>Harga</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="detailItems">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalPayment">
        <div class="modal-dialog">
            <form id="formPayment">
                @csrf
                <input type="hidden" id="paymentDebtId">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5>Pembayaran Piutang</h5>
                        <button class="close" data-dismiss="modal">
                            &times;
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Sisa Hutang</label>
                            <input id="remainingDebt" class="form-control" readonly>
                        </div>
                        <div class="form-group">
                            <label>Nominal Dibayar</label>
                            <input type="number" id="payAmount" class="form-control" min="1000">
                            <small id="payAmountError" class="text-danger d-none"></small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-dismiss="modal">
                            Batal
                        </button>
                        <button class="btn btn-success">
                            Simpan Pembayaran
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            // TOAST
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });

            $('.btn-pay').click(function() {
                $('#paymentDebtId').val($(this).data('id'));
                $('#remainingDebt').val(
                    'Rp ' + parseInt($(this).data('amount')).toLocaleString('id-ID')
                );

                // SIMPAN NILAI ASLI
                $('#remainingDebt').attr('data-value', $(this).data('amount'));
                $('#payAmount').val('');
                $('#modalPayment').modal('show');
            });

            // DETAIL PIUTANG
            $('.btn-detail').click(function() {
                let id = $(this).data('id');
                $.get(
                    '/customer-debts/' + id + '/detail',
                    function(res) {
                        console.log(res);
                        let d = res.data;
                        // INFO
                        $('#detailInvoice')
                            .text(
                                d.sale.invoice_no ?? '-'
                            );

                        $('#detailDate')
                            .text(d.sale.sale_date);

                        $('#detailCustomer')
                            .text(
                                d.sale.customer
                                ?.customer_name ?? '-'
                            );
                        $('#detailPhone')
                            .text(
                                d.sale.customer?.phone ?? '-'
                            );
                        $('#detailCashier')
                            .text(
                                d.sale.user?.name ?? '-'
                            );

                        $('#detailStatus')
                            .html(
                                d.status == 'active' ?
                                '<span class="badge badge-danger">Belum Lunas</span>' :
                                '<span class="badge badge-success">Lunas</span>'
                            );
                        // TOTAL
                        $('#detailTotal').text('Rp ' +
                            parseInt(d.sale.total_amount)
                            .toLocaleString('id-ID')
                        );
                        // DIBAYAR
                        $('#detailPaid').text('Rp ' +
                            parseInt(d.sale.paid_amount ?? 0)
                            .toLocaleString('id-ID')
                        );

                        // CREDIT
                        if (d.sale.payment_type == 'credit') {
                            $('#debtWrapper').show();
                            $('#changeWrapper').hide();
                            $('#detailDebt').text('Rp ' +
                                parseInt(d.amount)
                                .toLocaleString('id-ID')

                            );

                        } else {
                            // CASH
                            $('#changeWrapper').show();
                            $('#debtWrapper').hide();
                            $('#detailChange').text('Rp ' +
                                parseInt(
                                    d.sale.change_amount ?? 0
                                ).toLocaleString('id-ID')

                            );

                        }
                        // ITEMS
                        let html = '';
                        d.sale.details.forEach(item => {
                            html += `
                    <tr>
                        <td>${item.product.product_name}</td>
                        <td>${item.quantity}</td>
                        <td>Rp ${parseInt(item.selling_price).toLocaleString('id-ID')}</td>
                        <td>Rp ${parseInt(item.subtotal).toLocaleString('id-ID')}</td>
                    </tr>
                `;
                        });

                        $('#detailItems').html(html);
                        // SHOW MODAL
                        $('#modalDetail').modal('show');
                    }
                );
            });

            $('#formPayment').submit(function(e) {

                e.preventDefault();

                let id = $('#paymentDebtId').val();
                let sisa = parseFloat($('#remainingDebt').attr('data-value'));
                let bayar = parseFloat($('#payAmount').val());

                // Reset error
                $('#payAmount').removeClass('is-invalid');
                $('#payAmountError').addClass('d-none').text('');

                // Validasi kosong
                if ($('#payAmount').val().trim() === '') {
                    $('#payAmount').addClass('is-invalid');
                    $('#payAmountError')
                        .removeClass('d-none')
                        .text('Nominal pembayaran wajib diisi.');
                    $('#payAmount').focus();
                    return;
                }

                // Validasi <= 0
                if (bayar <= 0) {
                    $('#payAmount').addClass('is-invalid');
                    $('#payAmountError')
                        .removeClass('d-none')
                        .text('Nominal pembayaran harus lebih dari 0.');
                    $('#payAmount').focus();
                    return;
                }

                // Validasi melebihi sisa hutang
                if (bayar > sisa) {
                    $('#payAmount').addClass('is-invalid');
                    $('#payAmountError')
                        .removeClass('d-none')
                        .text('Nominal pembayaran tidak boleh melebihi sisa hutang.');
                    $('#payAmount').focus();
                    return;
                }

                $.ajax({
                    url: '/customer-debts/' + id + '/pay',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        amount: bayar
                    },

                    success: function(res) {
                        Toast.fire({
                            icon: 'success',
                            title: res.message
                        });
                        $('#modalPayment').modal('hide');
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    }
                });
            });
            $('#payAmount').on('input', function() {
                $(this).removeClass('is-invalid');
                $('#payAmountError')
                    .addClass('d-none')
                    .text('');
            });

            // FILTER PANEL
            $('#openFilter').click(function() {
                $('#filterPanel').addClass('active');
            });

            $('#closeFilter').click(function() {
                $('#filterPanel').removeClass('active');
            });

            // REALTIME SEARCH
            $('#searchInput').on('keyup', function() {
                let value =
                    $(this).val().toLowerCase();
                $('table tbody tr').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                });
            });

            // APPLY FILTER
            $('#applyFilter').click(function() {
                let status = $('input[name="status"]:checked').val();
                let from = $('#dateFrom').val();
                let to = $('#dateTo').val();

                $('table tbody tr').each(function() {
                    let row = $(this);
                    // STATUS
                    let rowStatus = row.data('status');
                    // TANGGAL
                    let rowDate = row.find('td:eq(1)').text().trim();

                    // FORMAT DATE
                    let parts = rowDate.split('/');
                    let formattedDate =
                        parts[2] + '-' +
                        parts[1] + '-' +
                        parts[0];
                    let show = true;

                    // FILTER STATUS
                    if (status) {
                        if (rowStatus != status) {
                            show = false;
                        }
                    }

                    // FILTER DATE FROM
                    if (from) {
                        if (formattedDate < from) {
                            show = false;
                        }
                    }

                    // FILTER DATE TO
                    if (to) {
                        if (formattedDate > to) {
                            show = false;
                        }
                    }
                    row.toggle(show);
                });
                // CLOSE PANEL
                $('#filterPanel')
                    .removeClass('active');
            });
            document.getElementById('perPageSelect').addEventListener('change', function() {
                const url = new URL(window.location.href);
                url.searchParams.set('per_page', this.value);
                url.searchParams.set('page', 1);
                window.location.href = url.toString();
            });
        </script>
    @endpush
@endsection
