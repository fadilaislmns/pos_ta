@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- HEADER -->
        <div class="d-flex justify-content-between align-items-center mb-3">

            <div>
                <h3 class="font-weight-bold">Master Pemasok</h3>

                <input type="text" id="searchSupplier" class="form-control" placeholder="Search supplier..."
                    style="width: 300px;">
            </div>

            <button class="btn btn-dark" data-toggle="modal" data-target="#modalAdd">
                <i class="fas fa-plus"></i> Tambah Pemasok
            </button>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <!-- TABLE -->
        <div class="card">
            <div class="card-body">

                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Suplier</th>
                            <th>Waktu Tunggu (Hari)</th>
                            <th>Jumlah Pemesanan Minimum</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody id="supplierTable">
                        @foreach ($suppliers as $i => $s)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $s->supplier_name }}</td>
                                <td>{{ $s->lead_time }}</td>
                                <td>{{ $s->MOQ }}</td>

                                <td>

                                    <!-- DETAIL -->
                                    <a href="#" class="action-icon text-info btn-detail"
                                        data-name="{{ $s->supplier_name }}" data-lead="{{ $s->lead_time }}"
                                        data-moq="{{ $s->MOQ }}" data-toggle="modal" data-target="#modalDetail">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <!-- EDIT -->
                                    <a href="#" class="action-icon text-warning btn-edit"
                                        data-id="{{ $s->id }}" data-name="{{ $s->supplier_name }}"
                                        data-lead="{{ $s->lead_time }}" data-moq="{{ $s->MOQ }}" data-toggle="modal"
                                        data-target="#modalEdit">
                                        <i class="fas fa-pen"></i>
                                    </a>

                                    <!-- DELETE -->
                                    <form action="{{ route('suppliers.destroy', $s->id) }}" method="POST"
                                        style="display:inline;">
                                        @csrf @method('DELETE')
                                        <button class="action-icon text-danger border-0 bg-transparent"
                                            onclick="return confirm('Hapus supplier ini?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>

                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>

            </div>
            {{-- PAGINATION DI DALAM CARD --}}
            <div class="card-footer d-flex justify-content-between align-items-center">

                <div class="d-flex align-items-center mt-3">

                    <span class="mr-2">Show</span>

                    <select class="form-control mr-2" style="width: 80px;" onchange="changeLimit(this.value)">
                        <option value="10" {{ request('limit') == 5 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('limit') == 10 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('limit') == 25 ? 'selected' : '' }}>50</option>
                    </select>

                    <span>
                        from {{ $suppliers->total() }}
                    </span>

                </div>
            </div>
        </div>

    </div>

    <!-- ================= ADD ================= -->
    <div class="modal fade" id="modalAdd">
        <div class="modal-dialog">
            <form action="{{ route('suppliers.store') }}" method="POST">
                @csrf

                <div class="modal-content">
                    <div class="modal-header">
                        <h5>Tambah </h5>
                    </div>

                    <div class="modal-body">
                       <div class="form-group">
    <label>Nama Pemasok</label>
    <input type="text" name="supplier_name" placeholder="Nama Pemasok"
        value="{{ old('supplier_name') }}"
        class="form-control 
        @if($errors->any() && session('modal') == 'add') is-invalid @endif">

    @if ($errors->any() && session('modal') == 'add')
        <small class="text-danger">
            {{ $errors->first('supplier_name') }}
        </small>
    @endif
</div>
                        <div class="form-group">
    <label>Lead Time (hari)</label>
    <input type="number" name="lead_time" placeholder="Waktu Tunggu (Hari)"
        value="{{ old('lead_time') }}"
        class="form-control 
        @if($errors->any() && session('modal') == 'add') is-invalid @endif">
</div>

<div class="form-group">
    <label>MOQ</label>
    <input type="number" name="MOQ" placeholder="Minimum Quantity Order"
        value="{{ old('MOQ') }}"
        class="form-control 
        @if($errors->any() && session('modal') == 'add') is-invalid @endif">
</div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-success">Simpan</button>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <!-- ================= EDIT ================= -->
    <div class="modal fade" id="modalEdit">
        <div class="modal-dialog">
            <form method="POST" id="formEdit">
                @csrf
                @method('PUT')

                <div class="modal-content">
                    <div class="modal-header">
                        <h5>Edit Pemasok</h5>
                    </div>

                    <div class="modal-body">
                       <div class="form-group">
    <label>Nama Pemasok</label>
    <input type="text" id="editName" name="supplier_name"
        value="{{ session('modal') == 'edit' ? old('supplier_name') : '' }}"
        class="form-control 
        @if($errors->any() && session('modal') == 'edit') is-invalid @endif">

    @if ($errors->any() && session('modal') == 'edit')
        <small class="text-danger">
            {{ $errors->first('supplier_name') }}
        </small>
    @endif
</div>
                        <div class="form-group">
    <label>Lead Time (hari)</label>
    <input type="number" id="editLead" name="lead_time"
        value="{{ session('modal') == 'edit' ? old('lead_time') : '' }}"
        class="form-control">
</div>

<div class="form-group">
    <label>MOQ</label>
    <input type="number" id="editMoq" name="MOQ"
        value="{{ session('modal') == 'edit' ? old('MOQ') : '' }}"
        class="form-control">
</div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-primary">Update</button>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <!-- ================= DETAIL ================= -->
    <div class="modal fade" id="modalDetail">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5>Detail Pemasok</h5>
                </div>

                <div class="modal-body">
                    <label>Nama Suplier</label>
                    <input type="text" id="detailName" class="form-control mb-2" readonly>
                    <label>Waktu Tunggu (Hari)</label>
                    <input type="text" id="detailLead" class="form-control mb-2" readonly>
                    <label>Jumlah Pesanan Minimum (MOQ)</label>
                    <input type="text" id="detailMoq" class="form-control" readonly>

                </div>

            </div>
        </div>
    </div>


    @push('scripts')
        <script>
            // SEARCH
            $('#searchSupplier').on('keyup', function() {
                let value = $(this).val().toLowerCase();

                $('#supplierTable tr').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                });
            });

            // EDIT
            $(document).on('click', '.btn-edit', function() {

                let id = $(this).data('id');

                $('#editName').val($(this).data('name'));
                $('#editLead').val($(this).data('lead'));
                $('#editMoq').val($(this).data('moq'));

                $('#formEdit').attr('action', '/suppliers/' + id);
            });

            // DETAIL
            $(document).on('click', '.btn-detail', function() {

                $('#detailName').val($(this).data('name'));
                $('#detailLead').val('Lead Time: ' + $(this).data('lead') + ' hari');
                $('#detailMoq').val('MOQ: ' + $(this).data('moq'));
            });

            $('#modalAdd').on('hidden.bs.modal', function () {
    $(this).find('input').val('');
    $(this).find('.text-danger').remove();
    $(this).find('.is-invalid').removeClass('is-invalid');
});

$('#modalEdit').on('hidden.bs.modal', function () {
    $(this).find('.text-danger').remove();
    $(this).find('.is-invalid').removeClass('is-invalid');
});
        </script>
        @if (session('modal') == 'add')
<script>
    $('#modalAdd').modal('show');
</script>
@endif

@if (session('modal') == 'edit')
<script>
    $('#modalEdit').modal('show');
</script>
@endif
    @endpush
@endsection
