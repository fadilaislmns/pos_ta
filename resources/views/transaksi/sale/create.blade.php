@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <h4 class="fw-bold mb-3">POS Kasir</h4>

        <div class="row g-3">

            <!-- LEFT -->
            <div class="col-lg-8">

                <div class="card shadow-sm border-0">

                    <!-- SEARCH -->
                    <div class="card-body pb-2">
                        <div class="row g-2">

                            <div class="col-md-8 position-relative">
                                <input type="text" id="search" class="form-control"
                                    placeholder="Scan barcode atau cari produk">

                                <div id="resultBox" class="list-group position-absolute w-100 shadow"
                                    style="z-index:999; display:none; max-height:300px; overflow:auto;">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <select id="category" class="form-control">
                                    <option value="">Semua Kategori</option>
                                    @foreach ($categories as $c)
                                        <option value="{{ $c->id }}">{{ $c->category_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>
                    </div>

                    <!-- CART -->
                    <div class="card-body pt-0" style="max-height:60vh; overflow:auto;">
                        <table class="table align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Produk</th>
                                    <th>Harga</th>
                                    <th width="130">Qty</th>
                                    <th>Subtotal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="cartTable">
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Kosong</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="card-footer d-flex justify-content-between">
                        <div>Total Item: <b id="totalItem">0</b></div>
                        <div>Total Qty: <b id="totalQty">0</b></div>
                    </div>

                </div>

                <!-- INFO TRANSAKSI -->
                <div class="card mt-3 p-3">
                    <div class="row">
                        <div class="col-md-4">
                            <small>No Transaksi</small>
                            <div><b>AUTO</b></div>
                        </div>
                        <div class="col-md-4">
                            <small>Tanggal</small>
                            <div><b id="tanggal"></b></div>
                        </div>
                        <div class="col-md-4">
                            <small>Kasir</small>
                            <div><b>User</b></div>
                        </div>
                    </div>
                    <!-- CUSTOMER -->
                    <div class="mt-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="mb-0">
                                Customer
                            </label>
                            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal"
                                data-target="#modalCustomer">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>

                        <!-- SEARCH -->
                        <div class="position-relative">
                            <div class="input-group">
                                <input type="text" id="customerSearch" class="form-control"
                                    placeholder="Cari customer...">
                                <button class="btn btn-outline-danger" type="button" onclick="removeCustomer()">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>

                            <!-- RESULT -->
                            <div id="customerResult" class="list-group position-absolute w-100 shadow"
                                style="
                                        z-index:999;
                                        display:none;
                                        max-height:250px;
                                        overflow:auto;
                                        top:100%;
                                        left:0;
                                    ">
                            </div>

                        </div>
                    </div>
                </div>

                <!-- METODE -->
                <div class="mt-3">
                    <div class="row text-center g-2">
                        <div class="col">
                            <div class="payment-btn active" onclick="setPayment('cash', this)">
                                <i class="fas fa-money-bill-wave"></i><br>
                                Cash
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="payment-btn" onclick="setPayment('qris', this)">
                                <i class="fas fa-qrcode"></i><br>
                                QRIS
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="payment-btn" onclick="setPayment('card', this)">
                                <i class="fas fa-credit-card"></i><br>
                                Card
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="payment-btn" onclick="setPayment('credit', this)">
                                <i class="fas fa-file-invoice-dollar"></i><br>
                                Credit
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 sticky-top" style="top:10px;">
                    <div class="card-header fw-bold">
                        Ringkasan Pembayaran
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <span id="subtotal">Rp 0</span>
                        </div>
                        <div class="mb-2">
                            <label>Diskon</label>
                            <input type="text" id="discount" class="form-control" value="0">
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Potongan</span>
                            <span id="discountText">Rp 0</span>
                        </div>
                        <hr>
                        <h3 class="text-primary fw-bold text-end">
                            <span>Total: </span>
                            Rp <span id="total">0</span>
                        </h3>
                        <label>Bayar</label>
                        <input type="text" id="bayar" class="form-control">
                        <div class="d-flex gap-2 mt-2">
                            <button class="btn btn-outline-secondary w-100" onclick="setBayar(20000)">20K</button>
                            <button class="btn btn-outline-secondary w-100" onclick="setBayar(50000)">50K</button>
                            <button class="btn btn-outline-secondary w-100" onclick="setBayar(100000)">100K</button>
                        </div>

                        <label class="mt-3 text-success">Kembalian</label>
                        <input type="text" id="kembali" class="form-control bg-light" readonly>

                        <button class="btn btn-primary w-100 mt-4" onclick="checkout()">
                            Proses Pembayaran
                        </button>

                    </div>

                </div>

            </div>

        </div>
    </div>
    //MODAL CUSTOMER
    <div class="modal fade" id="modalCustomer" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- HEADER -->
                <div class="modal-header">
                    <h5 class="modal-title">
                        Tambah Customer
                    </h5>

                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <!-- BODY -->
                <form id="formCustomer">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>
                                Nama Customer
                            </label>
                            <input type="text" name="customer_name" class="form-control" placeholder="Nama customer">
                            <small class="text-danger error-name"></small>
                        </div>

                        <div class="form-group">
                            <label>
                                No HP
                            </label>
                            <input type="text" name="phone" class="form-control" placeholder="08xxxx">
                            <small class="text-danger error-phone"></small>
                        </div>
                    </div>

                    <!-- FOOTER -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            Batal
                        </button>

                        <button type="submit" class="btn btn-primary">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });

            let allProducts = @json($products);
            let products = [...allProducts];

            let cart = [];
            let paymentType = 'cash';

            let grandTotal = 0;
            let selectedCustomer = null;
            let customers = @json($customers);

            // FORMAT RUPIAH
            function rupiah(angka) {
                return 'Rp ' + Number(angka).toLocaleString('id-ID');
            }

            function angka(value) {
                return parseInt(value.replace(/[^0-9]/g, '')) || 0;
            }

            // TANGGAL
            document.getElementById('tanggal').innerText =
                new Date().toLocaleDateString('id-ID');

            // SEARCH PRODUK
            document.getElementById('search').addEventListener('keyup', function() {

                let val = this.value.toLowerCase();
                let box = document.getElementById('resultBox');

                if (val.length < 1) {
                    box.style.display = 'none';
                    return;
                }

                let filtered = products.filter(p =>
                    p.product_name.toLowerCase().includes(val)
                );

                box.innerHTML = filtered.map(p => `
                <a href="#"
                    class="list-group-item list-group-item-action"
                    onclick="selectProduct(${p.id})">

                    <div class="d-flex justify-content-between">
                        <span>${p.product_name}</span>
                        <small class="text-muted">
                            ${rupiah(p.selling_price)}
                        </small>
                    </div>

                </a>
            `).join('');

                box.style.display =
                    filtered.length ? 'block' : 'none';
            });

            // PILIH PRODUK
            function selectProduct(id) {

                let p = products.find(x => x.id === id);

                if (!p) return;

                let found = cart.find(x => x.id === id);

                if (found) {
                    found.qty++;
                } else {
                    cart.push({
                        id: p.id,
                        name: p.product_name,
                        price: Number(p.selling_price),
                        qty: 1
                    });
                }

                render();

                document.getElementById('search').value = '';
                document.getElementById('resultBox').style.display = 'none';
            }

            // SEARCH CUSTOMER
            document.getElementById('customerSearch')
                .addEventListener('keyup', function() {
                    let val = this.value.toLowerCase();
                    let box = document.getElementById('customerResult');
                    if (val.length < 1) {
                        box.style.display = 'none';
                        return;
                    }

                    let filtered = customers.filter(c =>
                        c.customer_name.toLowerCase().includes(val)
                    );

                    box.innerHTML = filtered.map(c => `
                <a href="#"
                    class="list-group-item list-group-item-action"
                    onclick="selectCustomer(${c.id})">
                    <div class="fw-bold">
                        ${c.customer_name}
                    </div>
                </a>
            `).join('');
                    box.style.display =
                        filtered.length ? 'block' : 'none';
                });

            // SELECT CUSTOMER
            function selectCustomer(id) {
                let c = customers.find(x => x.id === id);
                if (!c) return;
                selectedCustomer = c;
                document.getElementById('customerSearch')
                    .value = c.customer_name;
                document.getElementById('customerResult')
                    .style.display = 'none';
            }

            // REMOVE CUSTOMER
            function removeCustomer() {
                selectedCustomer = null;
                document.getElementById('customerSearch')
                    .value = '';
            }

            // TAMBAH CUSTOMER
            $('#formCustomer').submit(function(e) {
                e.preventDefault();
                $('.error-name').text('');
                $('.error-phone').text('');
                $.ajax({
                    url: '/customers',
                    type: 'POST',
                    data: $(this).serialize() + '&_token={{ csrf_token() }}',
                    success: function(res) {
                        // CLOSE MODAL
                        $('#modalCustomer').modal('hide');
                        // RESET FORM
                        $('#formCustomer')[0].reset();
                        // ADD TO ARRAY
                        customers.push({
                            id: res.data.id,
                            customer_name: res.data.customer_name,
                            phone: res.data.phone
                        });

                        // AUTO SELECT
                        selectedCustomer = {
                            id: res.data.id,
                            customer_name: res.data.customer_name,
                            phone: res.data.phone
                        };

                        // SET INPUT
                        $('#customerSearch')
                            .val(res.data.customer_name);
                        // SUCCESS
                        Toast.fire({
                            icon: 'success',
                            title: 'Transaksi berhasil'
                        });
                    },

                    error: function(xhr) {
                        console.log(xhr);
                        // VALIDATION
                        if (xhr.status == 422) {
                            let errors = xhr.responseJSON.errors;
                            if (errors.customer_name) {
                                $('.error-name')
                                    .text(errors.customer_name[0]);
                            }

                            if (errors.phone) {
                                $('.error-phone')
                                    .text(errors.phone[0]);
                            }
                        } else {
                            Toast.fire({
                                icon: 'error',
                                title: 'Transaksi gagal'
                            });
                        }
                    }
                });
            });

            // FILTER KATEGORI
            document.getElementById('category').addEventListener('change', function() {

                let id = this.value;

                products = id ?
                    allProducts.filter(p => p.category_id == id) :
                    [...allProducts];
            });

            // RENDER CART
            function render() {

                let html = '';

                let subtotal = 0;
                let totalQty = 0;

                cart.forEach((item, index) => {

                    let sub = item.price * item.qty;

                    subtotal += sub;
                    totalQty += item.qty;

                    html += `
                <tr>

                    <td>${item.name}</td>

                    <td>${rupiah(item.price)}</td>

                    <td>
                        <div class="qty-box">

                            <button class="qty-btn"
                                onclick="chg(${index},-1)">
                                -
                            </button>

                            <div class="qty-number">
                                ${item.qty}
                            </div>

                            <button class="qty-btn"
                                onclick="chg(${index},1)">
                                +
                            </button>

                        </div>
                    </td>

                    <td>${rupiah(sub)}</td>

                    <td>
                        <button class="btn-delete"
                            onclick="del(${index})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                `;
                });

                // DISKON
                let discount =
                    parseInt(document.getElementById('discount')?.value || 0);

                // TOTAL
                grandTotal = subtotal - discount;

                if (grandTotal < 0) {
                    grandTotal = 0;
                }

                // TABEL
                document.getElementById('cartTable').innerHTML =
                    html || `
                <tr>
                    <td colspan="5"
                        class="text-center text-muted">
                        Kosong
                    </td>
                </tr>`;

                // SUMMARY
                document.getElementById('subtotal').innerText =
                    rupiah(subtotal);

                document.getElementById('discountText').innerText =
                    rupiah(discount);

                document.getElementById('total').innerText =
                    grandTotal.toLocaleString('id-ID');

                document.getElementById('totalItem').innerText =
                    cart.length;

                document.getElementById('totalQty').innerText =
                    totalQty;

                hitung();
            }

            // QTY
            function chg(index, val) {

                cart[index].qty += val;

                if (cart[index].qty <= 0) {
                    cart.splice(index, 1);
                }

                render();
            }

            // DELETE
            function del(index) {

                cart.splice(index, 1);

                render();
            }

            // DISKON
            document.getElementById('discount')
                ?.addEventListener('input', render);

            // PAYMENT
            function setPayment(type, el) {

                paymentType = type;

                document.querySelectorAll('.payment-btn')
                    .forEach(btn => btn.classList.remove('active'));

                el.classList.add('active');
            }

            // QUICK BAYAR
            function setBayar(v) {
                document.getElementById('bayar').value = rupiah(v);
                hitung();
            }

            // INPUT BAYAR
            document.getElementById('bayar')
                .addEventListener('input', function() {

                    let angkaBayar = angka(this.value);

                    this.value = angkaBayar ?
                        rupiah(angkaBayar) :
                        '';

                    hitung();
                });

            // HITUNG KEMBALIAN
            function hitung() {
                let bayar = angka(document.getElementById('bayar').value);
                let kembali = bayar - grandTotal;

                document.getElementById('kembali').value =
                    kembali > 0 ? rupiah(kembali) : rupiah(0);
            }

            // CHECKOUT
            function checkout() {

                if (cart.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Oops',
                        text: 'Keranjang masih kosong'
                    });
                    return;
                }

                if (paymentType === 'credit' && !selectedCustomer) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Customer diperlukan',
                        text: 'Pilih customer untuk transaksi hutang'
                    });
                    return;
                }

                let bayar =
                    angka(document.getElementById('bayar').value);

                // SELAIN CREDIT WAJIB LUNAS
                if (
                    paymentType !== 'credit' && bayar < grandTotal
                ) {
                    Toast.fire({
                        icon: 'warning',
                        title: 'Pembayaran kurang'
                    });

                    return;
                }

                fetch('/api/sales', {

                        method: 'POST',

                        headers: {
                            'Content-Type': 'application/json'
                        },

                        body: JSON.stringify({
                            paid_amount: bayar,
                            sale_date: new Date().toISOString().slice(0, 10),
                            payment_type: paymentType,
                            customer_id: selectedCustomer ? selectedCustomer.id : null,
                            user_id: 1,
                            discount: parseInt(
                                document.getElementById('discount')?.value || 0
                            ),
                            total: grandTotal,
                            kembali: bayar - grandTotal,
                            items: cart.map(i => ({
                                product_id: i.id,
                                quantity: i.qty
                            }))

                        })
                    })
                    .then(res => res.json())
                    .then(res => {
                        if (res.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Transaksi Berhasil',
                                text: 'Pembayaran berhasil diproses',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            cart = [];
                            render();
                            document.getElementById('bayar').value = '';
                            document.getElementById('kembali').value = '';

                            // RESET CUSTOMER
                            removeCustomer();
                            paymentType = 'cash';
                            document.querySelectorAll('.payment-btn')
                                .forEach(btn => btn.classList.remove('active'));
                            document.querySelector('.payment-btn')
                                .classList.add('active');

                            if (document.getElementById('discount')) {
                                document.getElementById('discount').value = 0;
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: res.message || 'Gagal transaksi'
                            });
                        }
                    })

                    .catch(err => {
                        console.log(err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Server Error',
                            text: 'Terjadi kesalahan server'
                        });
                    });
            }

            // INIT
            render();
        </script>
    @endpush
@endsection
