@extends('layouts.app')

@section('content')
    <div class="card">


        <div class="card-header">
            <h4>Edit Transaksi Penjualan</h4>
        </div>

        <div class="card-body">

            <form action="{{ route('sales.update', $sale->id) }}" method="POST">

                @csrf
                @method('PUT')

                <div class="row mb-3">

                    <div class="col-md-4">
                        <label>Invoice</label>
                        <input type="text" class="form-control" value="{{ $sale->invoice_no }}" readonly>
                    </div>

                    <div class="col-md-4">
                        <label>Tanggal</label>
                        <input type="date" name="sale_date" class="form-control" value="{{ $sale->sale_date }}">
                    </div>

                    <div class="col-md-4">
                        <label>Customer</label>
                        <input type="text" class="form-control" value="{{ $sale->customer->customer_name ?? 'Umum' }}"
                            readonly>
                    </div>

                </div>

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Harga Normal</th>
                            <th>Diskon</th>
                            <th>Harga Jual</th>
                            <th>Qty</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($sale->details as $detail)
                            <tr>
                                <td>
                                    {{ $detail->product->product_name }}
                                    <input type="hidden" name="products[{{ $loop->index }}][product_id]"
                                        value="{{ $detail->product_id }}">
                                </td>
                                @php
                                    $hargaNormal = $detail->selling_price + $detail->discount_amount;
                                @endphp
                                <td>Rp {{ number_format($hargaNormal, 0, ',', '.') }}</td>
                                <td>
                                    @if ($detail->discount_amount > 0)
                                        Rp {{ number_format($detail->discount_amount, 0, ',', '.') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="price" data-price="{{ $detail->selling_price }}">
                                    Rp {{ number_format($detail->selling_price, 0, ',', '.') }}
                                </td>
                                <td>
                                    <input type="number" min="1" class="form-control qty"
                                        name="products[{{ $loop->index }}][quantity]" value="{{ $detail->quantity }}">
                                </td>
                                <td class="subtotal">
                                    Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="row mt-4">
                    <div class="col-md-4">
                        <label>Total Harga</label>
                        <input type="text" id="total" class="form-control" readonly>
                    </div>
                    <div class="col-md-4">
                        <label>Uang Dibayar</label>
                        <input type="text" class="form-control"
                            value="Rp {{ number_format($sale->paid_amount, 0, ',', '.') }}" readonly>
                    </div>
                    <div class="col-md-4">
                        <label>Kembalian</label>
                        <input type="text" id="change" class="form-control" readonly>
                    </div>
                </div>
                <div class="mt-4 d-flex">
                    <button class="btn btn-primary">
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('sales.show', $sale->id) }}" class="btn btn-secondary ml-2">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function formatRupiah(angka) {
                return 'Rp ' +
                    angka.toLocaleString('id-ID');
            }

            function calculateTotal() {

                let total = 0;

                document.querySelectorAll("tbody tr")
                    .forEach(function(row) {
                        let priceElement = row.querySelector(".price");
                        if (!priceElement) return;
                        let price = parseFloat(priceElement.dataset.price);
                        let qty = parseInt(row.querySelector(".qty").value || 0);
                        let subtotal = price * qty;
                        row.querySelector(".subtotal").innerText = formatRupiah(subtotal);
                        total += subtotal;
                    });
                document.getElementById('total').value = formatRupiah(total);
                let paid = {{ $sale->paid_amount ?? 0 }};
                let change = paid - total;
                document.getElementById('change').value = formatRupiah(Math.max(0, change));
            }

            document
                .querySelectorAll('.qty')
                .forEach(function(input) {
                    input.addEventListener(
                        'keyup',
                        calculateTotal
                    );
                    input.addEventListener(
                        'change',
                        calculateTotal
                    );
                });
            calculateTotal();
        </script>
    @endpush
@endsection
