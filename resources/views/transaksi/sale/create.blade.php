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

                        <div class="col-md-5 position-relative">
                            <input type="text" id="search"
                                class="form-control"
                                placeholder="Scan barcode atau cari produk">

                            <div id="resultBox"
                                class="list-group position-absolute w-100 shadow"
                                style="z-index:999; display:none; max-height:300px; overflow:auto;">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <input type="text" id="searchName"
                                class="form-control"
                                placeholder="Cari nama produk">
                        </div>

                        <div class="col-md-3">
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

                    <div class="d-flex justify-content-between mb-2">
                        <span>Diskon</span>
                        <span>Rp 0</span>
                    </div>

                    <hr>

                    <h3 class="text-primary fw-bold text-end">
                        Rp <span id="total">0</span>
                    </h3>

                    <label>Bayar</label>
                    <input type="number" id="bayar" class="form-control">

                    <div class="d-flex gap-2 mt-2">
                        <button class="btn btn-outline-secondary w-100" onclick="setBayar(20000)">20K</button>
                        <button class="btn btn-outline-secondary w-100" onclick="setBayar(50000)">50K</button>
                        <button class="btn btn-outline-secondary w-100" onclick="setBayar(100000)">100K</button>
                    </div>

                    <label class="mt-3 text-success">Kembalian</label>
                    <input type="text" id="kembali" class="form-control bg-light" readonly>

                    <!-- METODE -->
                    <div class="mt-3">
                        <div class="row text-center">

                            <div class="col">
                                <div class="payment-btn active" onclick="setPayment('cash', this)">
                                    <i class="fas fa-money-bill-wave"></i><br>
                                    Cash
                                </div>
                            </div>

                            <div class="col">
                                <div class="payment-btn" onclick="setPayment('qris', this)">
                                    <i class="fas fa-qrcode"></i><br>
                                    QRIS
                                </div>
                            </div>

                            <div class="col">
                                <div class="payment-btn" onclick="setPayment('card', this)">
                                    <i class="fas fa-credit-card"></i><br>
                                    Card
                                </div>
                            </div>

                        </div>
                    </div>

                    <button class="btn btn-primary w-100 mt-4" onclick="checkout()">
                        Proses Pembayaran
                    </button>

                </div>

            </div>

        </div>

    </div>
</div>

<style>
.qty-box {
    display:flex;
    align-items:center;
    gap:6px;
}

.qty-btn {
    width:30px;
    height:30px;
    border:none;
    background:#f1f1f1;
    border-radius:6px;
}

.qty-number {
    min-width:20px;
    text-align:center;
    font-weight:bold;
}

.btn-delete {
    width:30px;
    height:30px;
    border:none;
    background:#dc3545;
    color:white;
    border-radius:6px;
}

.payment-btn {
    border:1px solid #ddd;
    border-radius:10px;
    padding:10px;
    cursor:pointer;
}

.payment-btn.active {
    border:2px solid #0d6efd;
    background:#e7f1ff;
}
</style>

<script>

let allProducts = @json($products);
let products = [...allProducts];
let cart = [];
let paymentType = 'cash';

// tanggal
document.getElementById('tanggal').innerText =
    new Date().toLocaleDateString();

// SEARCH
document.getElementById('search').addEventListener('keyup', function(){
    let val = this.value.toLowerCase();
    let box = document.getElementById('resultBox');

    if(val.length < 2){
        box.style.display='none';
        return;
    }

    let filtered = products.filter(p =>
        p.product_name.toLowerCase().includes(val)
    );

    box.innerHTML = filtered.map(p => `
        <a href="#" class="list-group-item"
            onclick="selectProduct(${p.id})">
            ${p.product_name}
            <small class="text-muted">Rp ${p.selling_price}</small>
        </a>
    `).join('');

    box.style.display='block';
});

// SELECT
function selectProduct(id){
    let p = products.find(x=>x.id===id);
    let f = cart.find(x=>x.id===id);

    if(f) f.qty++;
    else cart.push({id:p.id,name:p.product_name,price:p.selling_price,qty:1});

    render();
    document.getElementById('search').value='';
    document.getElementById('resultBox').style.display='none';
}

// CATEGORY
document.getElementById('category').addEventListener('change', function(){
    let id = this.value;
    products = id ? allProducts.filter(p=>p.category_id==id) : [...allProducts];
});

// RENDER
function render(){

    let html='', total=0, qty=0;

    cart.forEach((i,x)=>{
        let sub=i.qty*i.price;
        total+=sub;
        qty+=i.qty;

        html+=`
        <tr>
            <td>${i.name}</td>
            <td>Rp ${i.price.toLocaleString()}</td>
            <td>
                <div class="qty-box">
                    <button class="qty-btn" onclick="chg(${x},-1)">-</button>
                    <div class="qty-number">${i.qty}</div>
                    <button class="qty-btn" onclick="chg(${x},1)">+</button>
                </div>
            </td>
            <td>Rp ${sub.toLocaleString()}</td>
            <td>
                <button class="btn-delete" onclick="del(${x})">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>`;
    });

    document.getElementById('cartTable').innerHTML =
        html || `<tr><td colspan="5">Kosong</td></tr>`;

    document.getElementById('total').innerText = total.toLocaleString();
    document.getElementById('subtotal').innerText = "Rp " + total.toLocaleString();
    document.getElementById('totalItem').innerText = cart.length;
    document.getElementById('totalQty').innerText = qty;

    hitung();
}

// QTY
function chg(i,v){
    cart[i].qty+=v;
    if(cart[i].qty<=0) cart.splice(i,1);
    render();
}
function del(i){
    cart.splice(i,1);
    render();
}

// PAYMENT
function setPayment(type, el){
    paymentType = type;
    document.querySelectorAll('.payment-btn').forEach(b=>b.classList.remove('active'));
    el.classList.add('active');
}

// HITUNG
document.getElementById('bayar').addEventListener('input', hitung);
function setBayar(v){
    document.getElementById('bayar').value=v;
    hitung();
}
function hitung(){
    let t=parseInt(document.getElementById('total').innerText.replace(/\./g,''))||0;
    let b=parseInt(document.getElementById('bayar').value)||0;
    document.getElementById('kembali').value = b>t ? (b-t) : 0;
}

// CHECKOUT
function checkout(){

    if(cart.length===0){
        alert('Keranjang kosong');
        return;
    }

    fetch('/api/sales',{
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body: JSON.stringify({
            sale_date:new Date().toISOString().slice(0,10),
            payment_type:paymentType,
            user_id:1,
            items:cart.map(i=>({
                product_id:i.id,
                quantity:i.qty
            }))
        })
    })
    .then(res=>res.json())
    .then(res=>{
        if(res.success){
            alert('Transaksi berhasil');
            cart=[];
            render();
            document.getElementById('bayar').value='';
            document.getElementById('kembali').value='';
        }else{
            alert(res.message);
        }
    })
    .catch(()=>alert('Server error'));
}

</script>

@endsection
