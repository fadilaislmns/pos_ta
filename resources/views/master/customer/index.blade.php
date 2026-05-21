@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- HEADER -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h3 class="font-weight-bold">Master Customer</h3>
                <input type="text" id="searchCustomer" class="form-control" placeholder="Search customer..."
                    style="width:300px;">
            </div>

            <button class="btn btn-dark" data-toggle="modal" data-target="#modalAdd">
                <i class="fas fa-plus"></i> Tambah Pelanggan
            </button>
        </div>

        <!-- TABLE -->
        <div class="card">
            <div class="card-body">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Pelanggan</th>
                            <th>No HP</th>
                            <th width="120">Aksi</th>
                        </tr>
                    </thead>

                    <tbody id="customerTable">

                        @foreach ($customers as $i => $c)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $c->customer_name }}</td>
                                <td>{{ $c->phone }}</td>
                                <td>
                                    <!-- EDIT -->
                                    <a href="#" class="action-icon text-warning btn-edit"
                                        data-customer='@json($c)'>
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <!-- DELETE -->
                                    <a href="#" class="action-icon text-danger border-0 bg-transparent btn-delete"
                                        data-id="{{ $c->id }}">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL ADD -->
    <div class="modal fade" id="modalAdd" tabindex="-1">
        <div class="modal-dialog">
            <form id="formAdd">
                @csrf
                <div class="modal-content">
                    <!-- HEADER -->
                    <div class="modal-header">
                        <h4 class="modal-title font-weight-bold">
                            Tambah Customer
                        </h4>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>

                    <!-- BODY -->
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nama Customer</label>
                            <input type="text" name="customer_name" class="form-control" placeholder="Nama Customer">
                        </div>

                        <div class="form-group">
                            <label>No HP</label>
                            <input type="text" name="phone" class="form-control" placeholder="08xxxxxxxxxx">
                        </div>
                    </div>

                    <!-- FOOTER -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            Batal
                        </button>
                        <button class="btn btn-success">
                            Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT -->
    <div class="modal fade" id="modalEdit" tabindex="-1">
        <div class="modal-dialog">
            <form id="formEdit">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <!-- HEADER -->
                    <div class="modal-header">
                        <h4 class="modal-title font-weight-bold">
                            Edit Customer
                        </h4>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>

                    <!-- BODY -->
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nama Customer</label>
                            <input type="text" id="editName" name="customer_name" class="form-control">
                        </div>

                        <div class="form-group">
                            <label>No HP</label>
                            <input type="text" id="editPhone" name="phone" class="form-control">
                        </div>
                    </div>

                    <!-- FOOTER -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            Batal
                        </button>

                        <button class="btn btn-primary">
                            Update
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                }
            });
            $(document).ready(function() {

                // SEARCH
                $('#searchCustomer').on('keyup', function() {
                    let value = $(this).val().toLowerCase();
                    $('#customerTable tr').filter(function() {
                        $(this).toggle(
                            $(this).text().toLowerCase().indexOf(value) > -1
                        );
                    });
                });

                // ADD
                $('#formAdd').submit(function(e) {
                    e.preventDefault();
                    $.ajax({
                        url: "{{ route('customers.store') }}",
                        type: "POST",
                        data: $(this).serialize(),
                        success: function(res) {
                            $('#modalAdd').modal('hide');
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: 'Customer berhasil ditambahkan',
                                showConfirmButton: false,
                                timer: 3500,
                                timerProgressBar: true
                            });
                            setTimeout(() => {
                                location.reload();
                            }, 1000);
                        },

                        error: function(xhr) {
                            console.log(xhr);
                            if (xhr.status == 422) {

                                let errors = xhr.responseJSON.errors;

                                // reset error lama
                                $('.text-danger').remove();
                                $('.is-invalid').removeClass('is-invalid');

                                $.each(errors, function(field, messages) {

                                    let input = $('[name="' + field + '"]');

                                    input.addClass('is-invalid');

                                    input.after(`
                                        <small class="text-danger">
                                            ${messages[0]}
                                        </small>
                                    `);

                                });

                            } else {
                                Swal.fire({
                                    toast: true,
                                    position: 'top-end',
                                    icon: 'error',
                                    title: xhr.responseJSON.message || 'Server Error',
                                    showConfirmButton: false,
                                    timer: 3500,
                                    timerProgressBar: true
                                });
                            }
                        }
                    });
                });

                // EDIT BUTTON
                $('.btn-edit').click(function() {
                    let data = $(this).data('customer');
                    $('#editName').val(data.customer_name);
                    $('#editPhone').val(data.phone);
                    $('#formEdit').attr(
                        'action',
                        '/customers/' + data.id
                    );
                    $('#modalEdit').modal('show');
                });

                // UPDATE
                $('#formEdit').submit(function(e) {
                    e.preventDefault();
                    let url = $(this).attr('action');
                    $.ajax({
                        url: url,
                        type: "POST",
                        data: $(this).serialize() + '&_method=PUT',
                        success: function(res) {
                            $('#modalEdit').modal('hide');
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: 'Customer berhasil diupdate',
                                showConfirmButton: false,
                                timer: 3500,
                                timerProgressBar: true
                            });
                            setTimeout(() => {
                                location.reload();
                            }, 1000);
                        },

                        error: function(xhr) {
                            if (xhr.status == 422) {
                                let errors = xhr.responseJSON.errors;
                                $('#modalEdit .text-danger').remove();
                                $('#modalEdit .is-invalid').removeClass('is-invalid');

                                $.each(errors, function(field, messages) {
                                    let input = $('#modalEdit').find('[name="' + field +
                                        '"]');
                                    input.addClass('is-invalid');
                                    input.after(`
                                        <small class="text-danger">
                                            ${messages[0]}
                                        </small>
                                    `);
                                });
                            } else {
                                Swal.fire({
                                    toast: true,
                                    position: 'top-end',
                                    icon: 'error',
                                    title: 'Server Error',
                                    showConfirmButton: false,
                                    timer: 3500,
                                    timerProgressBar: true
                                });
                            }
                        }
                    });
                });

                // DELETE
                $('.btn-delete').click(function() {
                    let id = $(this).data('id');
                    Swal.fire({
                        icon: 'warning',
                        title: 'Hapus customer ini?',
                        text: 'Data tidak bisa dikembalikan',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Hapus',
                        cancelButtonText: 'Batal',
                        width: '360px'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.post('/customers/' + id, {
                                _token: '{{ csrf_token() }}',
                                _method: 'DELETE'
                            }, function(res) {
                                Swal.fire({
                                    toast: true,
                                    position: 'top-end',
                                    icon: 'success',
                                    title: res.message,
                                    showConfirmButton: false,
                                    timer: 3500,
                                    timerProgressBar: true
                                });
                                setTimeout(() => {
                                    location.reload();
                                }, 1000);

                            });

                        }

                    });

                });
            });
        </script>
    @endpush
@endsection
