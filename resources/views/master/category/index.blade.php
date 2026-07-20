@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-3">

            <div>
                <h3 class="mb-2 font-weight-bold">Master Kategori</h3>
                <!-- SEARCH -->
                <input type="text" id="searchCategory" class="form-control" placeholder="Cari Kategori..."
                    style="width: 300px;">
            </div>

            <div class="d-flex">
                <!-- ADD -->
                <button class="btn btn-dark" data-toggle="modal" data-target="#modalAdd">
                    <i class="fas fa-plus"></i> Tambah
                </button>

            </div>

        </div>

        <div class="card">
            <div class="card-body">

                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Kategori</th>
                            <th>Deskripsi</th>
                            <th width="180">Aksi</th>
                        </tr>
                    </thead>

                    <tbody id="categoryTable">
                        @foreach ($categories as $i => $c)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $c->category_name }}</td>
                                <td>{{ $c->description }}</td>
                                <td>

                                    <!-- DETAIL -->
                                    <a href="#" class="action-icon text-info btn-detail"
                                        data-name="{{ $c->category_name }}" data-desc="{{ $c->description }}"
                                        data-toggle="modal" data-target="#modalDetail">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <!-- EDIT -->
                                    <a href="#" class="action-icon text-warning btn-edit"
                                        data-id="{{ $c->id }}" data-name="{{ $c->category_name }}"
                                        data-desc="{{ $c->description }}" data-toggle="modal" data-target="#modalEdit">
                                        <i class="fas fa-pen"></i>
                                    </a>

                                    <!-- DELETE -->
                                    <form action="{{ route('categories.destroy', $c->id) }}" method="POST"
                                        class="delete-form" style="display:inline;">
                                        @csrf
                                        @method('DELETE')

                                        <button type="button"
                                            class="action-icon text-danger border-0 bg-transparent btn-delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>

                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>
                <div class="row align-items-center mt-4">

                    <div class="col-md-6 text-muted small">
                        Menampilkan <strong>{{ $categories->firstItem() }}</strong> sampai
                        <strong>{{ $categories->lastItem() }}</strong> dari
                        <strong>{{ $categories->total() }}</strong> data
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
                            {{ $categories->appends(request()->query())->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    </div>

    <!-- ADD -->
    <div class="modal fade" id="modalAdd">
        <div class="modal-dialog">
            <form action="{{ route('categories.store') }}" method="POST">
                @csrf

                <div class="modal-content">
                    <div class="modal-header">
                        <h5>Tambah Kategori</h5>
                    </div>

                    <div class="modal-body px-4 py-4">
                        <div class="form-group mb-4">
                            <label class="font-weight-bold">
                                Nama Kategori <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="category_name" value="{{ old('category_name') }}"
                                class="form-control @error('category_name') is-invalid @enderror"
                                placeholder="Contoh: Makanan Ringan" required style="border-radius: 10px; height: 45px;">

                            @error('category_name')
                                <small class="text-danger font-weight-bold mt-2 d-block">
                                    {{ $message }}
                                </small>
                            @enderror
                        </div>

                        <div class="form-group mb-0">
                            <label class="font-weight-bold">Deskripsi</label>
                            <textarea name="description" class="form-control" rows="4" placeholder="Masukkan deskripsi singkat..."
                                style="border-radius: 10px;">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-success">Simpan</button>
                    </div>

                </div>
            </form>
        </div>
    </div>


    <!-- EDIT -->
    <div class="modal fade" id="modalEdit">
        <div class="modal-dialog">
            <form method="POST" id="formEdit">
                @csrf
                @method('PUT')

                <div class="modal-content">
                    <div class="modal-header">
                        <h5>Edit Kategori</h5>
                    </div>

                    <div class="modal-body">

                        <div class="form-group">
                            <label>Nama Kategori</label>
                            <input type="text" id="editName" name="category_name" value="{{ old('category_name') }}"
                                class="form-control
    @if ($errors->any() && session('modal') == 'edit') is-invalid @endif">
                            @if ($errors->any() && session('modal') == 'edit')
                                <small class="text-danger">
                                    {{ $errors->first() }}
                                </small>
                            @endif
                        </div>

                        <div class="form-group">
                            <label>Deskripsi</label>
                            <textarea id="editDesc" name="description" class="form-control">{{ old('description') }}</textarea>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-primary">Update</button>
                    </div>

                </div>
            </form>
        </div>
    </div>


    <!-- DETAIL -->
    <div class="modal fade" id="modalDetail">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5>Detail Kategori</h5>
                </div>

                <div class="modal-body">

                    <div class="form-group">
                        <label>Nama Kategori</label>
                        <input type="text" id="detailName" class="form-control" readonly>
                    </div>

                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea id="detailDesc" class="form-control" readonly></textarea>
                    </div>

                </div>

            </div>
        </div>
    </div>


    @push('scripts')
        <script>
            $(document).on('click', '.btn-edit', function() {

                let id = $(this).data('id');

                $('#editName').val($(this).data('name'));
                $('#editDesc').val($(this).data('desc'));

                $('#formEdit').attr('action', '/categories/' + id);
            });

            $(document).on('click', '.btn-detail', function() {

                $('#detailName').val($(this).data('name'));
                $('#detailDesc').val($(this).data('desc'));
            });

            $(document).on('click', '.btn-delete', function(e) {
                e.preventDefault();
                let form = $(this).closest('form');
                Swal.fire({
                    title: 'Hapus kategori?',
                    text: 'Data tidak bisa dikembalikan',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Hapus',
                    cancelButtonText: 'Batal',
                    width: '320px',
                    padding: '1.5rem',
                    customClass: {
                        popup: 'rounded-lg',
                        title: 'text-sm',
                        htmlContainer: 'text-xs'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            $('#searchCategory').on('keyup', function() {
                let value = $(this).val().toLowerCase();

                $('#categoryTable tr').filter(function() {
                    $(this).toggle(
                        $(this).text().toLowerCase().indexOf(value) > -1
                    );
                });
            });

            $('#modalAdd').on('hidden.bs.modal', function() {
                $(this).find('input[type="text"], textarea').val('');
                $(this).find('.text-danger').remove();
            });

            $('button[data-target="#modalAdd"]').on('click', function() {
                let modal = $('#modalAdd');

                modal.find('input[type="text"], textarea').val('');
                modal.find('.text-danger').remove();
                modal.find('.is-invalid').removeClass('is-invalid');
            });

            $('#modalEdit').on('hidden.bs.modal', function() {
                $(this).find('input[type="text"], textarea').val('');
                $(this).find('.text-danger').remove();
                $(this).find('.is-invalid').removeClass('is-invalid');
            });

            document.getElementById('perPageSelect').addEventListener('change', function() {
                const url = new URL(window.location.href);
                url.searchParams.set('per_page', this.value);
                url.searchParams.set('page', 1);
                window.location.href = url.toString();
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

        @if (session('success'))
            <script>
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 2500,
                    timerProgressBar: true
                });
            </script>
        @endif
        @if (session('error'))
            <script>
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: '{{ session('error') }}',
                    showConfirmButton: false,
                    timer: 3000
                });
            </script>
        @endif
    @endpush
@endsection
