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
                <!-- FILTER -->
                <button class="btn btn-light mr-2">
                    <i class="fas fa-filter"></i> Filter
                </button>

                <!-- ADD -->
                <button class="btn btn-dark" data-toggle="modal" data-target="#modalAdd">
                    <i class="fas fa-plus"></i> Tambah
                </button>

            </div>

        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

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
                                        style="display:inline;">
                                        @csrf
                                        @method('DELETE')

                                        <button class="action-icon text-danger border-0 bg-transparent"
                                            onclick="return confirm('Hapus?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>

                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>

            </div>
            <div class="card-footer d-flex justify-content-between align-items-center">

                <div class="d-flex align-items-center mt-3">

                    <span class="mr-2">Show</span>

                    <select class="form-control mr-2" style="width: 80px;" onchange="changeLimit(this.value)">
                        <option value="10" {{ request('limit') == 5 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('limit') == 10 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('limit') == 25 ? 'selected' : '' }}>50</option>
                    </select>

                    <span>
                        from {{ $categories->total() }}
                    </span>

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

                    <div class="modal-body">

   
    <div class="form-group">
    <label>Nama Kategori *</label>

   <input type="text" 
       name="category_name" 
       value="{{ old('category_name') }}"
       class="form-control">

    @if ($errors->any() && session('modal') == 'add')
        <small class="text-danger">
            {{ $errors->first() }}
        </small>
    @endif
</div>

                        <div class="form-group">
                            <label>Deskripsi</label>
<textarea name="description" class="form-control">{{ old('description') }}</textarea>                        </div>

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
<input type="text" 
    id="editName" 
    name="category_name" 
    value="{{ old('category_name') }}"
    class="form-control 
    @if($errors->any() && session('modal') == 'edit') is-invalid @endif">
     @if ($errors->any() && session('modal') == 'edit')
    <small class="text-danger">
        {{ $errors->first() }}
    </small>
@endif
                        </div>

                        <div class="form-group">
                            <label>Deskripsi</label>
<textarea id="editDesc" name="description" class="form-control">{{ old('description') }}</textarea>                        </div>

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

            $('#searchCategory').on('keyup', function() {
                let value = $(this).val().toLowerCase();

                $('#categoryTable tr').filter(function() {
                    $(this).toggle(
                        $(this).text().toLowerCase().indexOf(value) > -1
                    );
                });
            });

          $('#modalAdd').on('hidden.bs.modal', function () {
    $(this).find('input[type="text"], textarea').val('');
    $(this).find('.text-danger').remove();
});

$('button[data-target="#modalAdd"]').on('click', function () {
    let modal = $('#modalAdd');

    modal.find('input[type="text"], textarea').val('');
    modal.find('.text-danger').remove();
    modal.find('.is-invalid').removeClass('is-invalid');
});

$('#modalEdit').on('hidden.bs.modal', function () {
    $(this).find('input[type="text"], textarea').val('');
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
