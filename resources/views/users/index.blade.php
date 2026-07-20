@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <div class="mb-4">
            <h3 class="font-weight-bold mb-0">
                User Management
            </h3>
        </div>
        <!-- TOP ACTION -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap" style="gap:12px;">
            <!-- SEARCH -->
            <div style="width:320px;">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white border-right-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                    </div>
                    <input type="text" id="searchUser" class="form-control border-left-0" placeholder="Cari user...">
                </div>
            </div>

            <!-- RIGHT BUTTON -->
            <div class="d-flex align-items-center" style="gap:12px;">
                <button type="button" id="btnOpenFilter" class="btn btn-outline-dark px-4">
                    <i class="fas fa-filter mr-1"></i>
                    Filter
                </button>

                <a href="{{ route('users.create') }}" class="btn btn-dark px-4">
                    <i class="fas fa-plus mr-1"></i>
                    Tambah
                </a>
            </div>
        </div>

        <!-- CARD -->
        <div class="card border-0 shadow-sm" style="border-radius:18px;">
            <div class="card-body">
                <!-- TABLE -->
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>

                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($users as $user)
                                <tr class="user-row" data-name="{{ strtolower($user->name) }}"
                                    data-email="{{ strtolower($user->email) }}"
                                    data-status="{{ strtolower($user->status) }}">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td style="font-weight:500;">
                                        {{ ucfirst(str_replace('_', ' ', $user->role->role_name)) }}</td>
                                    <td>
                                        <div class="d-flex align-items-center" style="gap:12px;">
                                            <div class="custom-switch-btn toggle-status-btn {{ $user->status == 'active' ? 'active' : '' }}"
                                                data-id="{{ $user->id }}" id="switch-{{ $user->id }}"
                                                style="cursor: pointer;">
                                                <div class="custom-switch-thumb"></div>
                                            </div>
                                            <span class="status-text" id="label-status-{{ $user->id }}">
                                                {{ ucfirst($user->status) }}
                                            </span>
                                        </div>
                                    </td>

                                    <!-- AKSI -->
                                    <td class="text-center">
                                        <!-- EDIT -->
                                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm text-warning">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                        <!-- RESET PASSWORD -->
                                        <button type="button" class="btn btn-sm text-secondary btn-reset-password"
                                            data-id="{{ $user->id }}" data-toggle="modal"
                                            data-target="#resetPasswordModal">
                                            <i class="fas fa-key"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty

                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        Data user kosong
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>
                <!-- PAGINATION -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted small">
                        Menampilkan <strong>{{ $users->firstItem() }}</strong> sampai
                        <strong>{{ $users->lastItem() }}</strong> dari
                        <strong>{{ $users->total() }}</strong> data
                    </div>

                    <div class="d-flex align-items-center" style="gap: 15px;">
                        <select id="perPageSelect" class="form-control" style="width:80px; border-radius:10px;">
                            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        </select>

                        <div>
                            {{ $users->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    </div>

    <div id="filterBackdrop" class="filter-backdrop"></div>

    <div id="filterSidePanel" class="filter-side-panel">

        <div class="border-bottom p-4 d-flex justify-content-between align-items-center">
            <h4 class="font-weight-bold mb-0 text-dark">Filter</h4>
            <button type="button" id="btnCloseFilter" class="close" aria-label="Close" style="font-size: 1.5rem;">
                <span aria-hidden="true"><i class="fas fa-times-circle text-dark"></i></span>
            </button>
        </div>

        <div class="p-4" style="flex-grow: 1; overflow-y: auto;">

            <p class="font-weight-bold mb-3 text-dark">Status</p>

            <div class="border p-3 rounded mb-4">
                <div class="custom-control custom-radio mb-2">
                    <input type="radio" id="statusActive" name="filterStatus" class="custom-control-input" value="active">
                    <label class="custom-control-label" for="statusActive">Active</label>
                </div>
                <div class="custom-control custom-radio">
                    <input type="radio" id="statusInactive" name="filterStatus" class="custom-control-input"
                        value="inactive">
                    <label class="custom-control-label" for="statusInactive">Inactive</label>
                </div>
            </div>

        </div>

        <div class="border-top p-4 d-flex justify-content-between align-items-center" style="gap: 12px;">
            <button type="button" id="resetFilter"
                class="btn btn-light border flex-fill py-2 font-weight-bold">Reset</button>
            <button type="button" id="applyFilter" class="btn btn-success flex-fill py-2 font-weight-bold"
                style="background-color: #208C2B; border: none;">Terapkan</button>
        </div>

    </div>

    <div class="modal fade" id="resetPasswordModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 5px 15px rgba(0,0,0,0.2);">

                <div class="modal-header border-bottom-0 pb-0 mt-2 mx-2">
                    <h4 class="font-weight-bold mb-0 text-dark">Change Password</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fas fa-times-circle text-muted"></i></span>
                    </button>
                </div>

                <div class="modal-body p-4">
                    <form id="formResetPassword" autocomplete="off">
                        <input type="hidden" id="reset_user_id">

                        <div class="form-group mb-3 position-relative">
                            <label class="font-weight-bold text-dark">New Password <span
                                    class="text-danger">*</span></label>
                            <input type="password" id="reset_password" class="form-control pr-5"
                                placeholder="Enter new password" required>
                            <i class="fas fa-eye toggle-password text-muted" toggle="#reset_password"></i>
                            <div class="invalid-feedback font-weight-bold" id="error-reset_password"></div>
                        </div>

                        <div class="form-group mb-4 position-relative">
                            <label class="font-weight-bold text-dark">Confirm Password <span
                                    class="text-danger">*</span></label>
                            <input type="password" id="reset_password_confirmation" class="form-control pr-5"
                                placeholder="Confirm new password" required>
                            <i class="fas fa-eye toggle-password text-muted" toggle="#reset_password_confirmation"></i>
                            <div class="invalid-feedback font-weight-bold" id="error-reset_password_confirmation"></div>
                        </div>

                        <div class="d-flex justify-content-end" style="gap: 12px;">
                            <button type="button" class="btn btn-outline-secondary px-4 font-weight-bold"
                                data-dismiss="modal" style="border-radius: 6px;">Batal</button>
                            <button type="submit" id="btnSubmitReset" class="btn btn-success px-4 font-weight-bold"
                                style="background-color: #208C2B; border: none; border-radius: 6px;">Save Changes</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });

            document.addEventListener('DOMContentLoaded', function() {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                @if (session()->has('success_message'))
                    Toast.fire({
                        icon: 'success',
                        title: "{!! session('success_message') !!}"
                    });
                @endif

                // 1. FITUR REAL-TIME SEARCH (Client-Side)
                const searchInput = document.getElementById('searchUser');
                const userRows = document.querySelectorAll('.user-row');
                // KONTROL SIDE PANEL FILTER
                const btnOpenFilter = document.getElementById('btnOpenFilter');
                const btnCloseFilter = document.getElementById('btnCloseFilter');
                const filterBackdrop = document.getElementById('filterBackdrop');
                const filterSidePanel = document.getElementById('filterSidePanel');

                function openFilter() {
                    filterBackdrop.classList.add('show');
                    filterSidePanel.classList.add('show');
                    document.body.style.overflow = 'hidden';
                }

                function closeFilter() {
                    filterBackdrop.classList.remove('show');
                    filterSidePanel.classList.remove('show');
                    document.body.style.overflow = '';
                }

                if (btnOpenFilter) btnOpenFilter.addEventListener('click', openFilter);
                if (btnCloseFilter) btnCloseFilter.addEventListener('click', closeFilter);
                if (filterBackdrop) filterBackdrop.addEventListener('click', closeFilter);

                // Simpan nilai search dan filter saat ini
                let currentSearchTerm = '';
                let currentStatusFilter = '';

                function applyTableFilters() {
                    userRows.forEach(row => {
                        const name = row.getAttribute('data-name');
                        const email = row.getAttribute('data-email');
                        const status = row.getAttribute('data-status');

                        const matchesSearch = name.includes(currentSearchTerm) || email.includes(
                            currentSearchTerm);

                        const matchesStatus = currentStatusFilter === '' || status === currentStatusFilter;

                        if (matchesSearch && matchesStatus) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                }

                if (searchInput) {
                    searchInput.addEventListener('input', function(e) {
                        currentSearchTerm = e.target.value.toLowerCase();
                        applyTableFilters();
                    });
                }

                const btnApplyFilter = document.getElementById('applyFilter');
                if (btnApplyFilter) {
                    btnApplyFilter.addEventListener('click', function() {
                        const selectedStatus = document.querySelector('input[name="filterStatus"]:checked');
                        currentStatusFilter = selectedStatus ? selectedStatus.value : '';

                        applyTableFilters();
                        closeFilter();
                    });
                }
                const btnResetFilter = document.getElementById('resetFilter');
                if (btnResetFilter) {
                    btnResetFilter.addEventListener('click', function(e) {
                        e.preventDefault(); // Mencegah pindah ke atas halaman

                        document.querySelectorAll('input[name="filterStatus"]').forEach(radio => {
                            radio.checked = false;
                        });

                        currentStatusFilter = '';
                        applyTableFilters();
                        closeFilter();
                    });
                }

                // 2. FITUR UPDATE STATUS
                const toggleButtons = document.querySelectorAll('.toggle-status-btn');
                toggleButtons.forEach(btn => {
                    btn.addEventListener('click', function(e) {
                        e.preventDefault();
                        const userId = this.getAttribute('data-id');
                        const label = document.getElementById(`label-status-${userId}`);
                        const isCurrentlyActive = this.classList.contains('active');

                        Swal.fire({
                            title: isCurrentlyActive ?
                                'Nonaktifkan pengguna?' : 'Aktifkan pengguna?',
                            text: isCurrentlyActive ?
                                'Apakah kamu yakin ingin menonaktifkan pengguna ini?' :
                                'Apakah kamu yakin ingin mengaktifkan pengguna ini?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Ya',
                            cancelButtonText: 'Batal',
                            confirmButtonColor: isCurrentlyActive ?
                                '#e3000f' : '#208C2B',
                            cancelButtonColor: '#6b7280'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                fetch(`/users/${userId}/toggle-status`, {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': csrfToken
                                        },
                                        body: JSON.stringify({})
                                    })
                                    .then(res => res.json())
                                    .then(data => {
                                        if (data.success) {
                                            this.classList.toggle('active');
                                            label.innerText = data.status.charAt(0)
                                                .toUpperCase() + data.status.slice(1);
                                            Toast.fire({
                                                icon: 'success',
                                                title: 'Status berhasil di perbarui.'
                                            });
                                        }
                                    });
                            }
                        });
                    });
                });

                // --- D. FITUR RESET PASSWORD ---
                const formResetPassword = document.getElementById('formResetPassword');
                const btnSubmitReset = document.getElementById('btnSubmitReset');

                document.querySelectorAll('.btn-reset-password').forEach(btn => {
                    btn.addEventListener('click', function() {
                        document.getElementById('reset_user_id').value = this.getAttribute('data-id');
                    });
                });

                formResetPassword.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const userId = document.getElementById('reset_user_id').value;
                    const password = document.getElementById('reset_password').value;
                    const confirm = document.getElementById('reset_password_confirmation').value;

                    if (password !== confirm) {
                        Swal.fire('Error', 'Password tidak cocok', 'error');
                        return;
                    }

                    btnSubmitReset.disabled = true;
                    btnSubmitReset.innerHTML = 'Saving...';

                    fetch(`/users/${userId}/reset-password`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                password: password,
                                password_confirmation: confirm
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                $('#resetPasswordModal').modal('hide');
                                Toast.fire({
                                    icon: 'success',
                                    title: data.message
                                });
                            } else {
                                Swal.fire('Error', data.message, 'error');
                            }
                        })
                        .finally(() => {
                            btnSubmitReset.disabled = false;
                            btnSubmitReset.innerHTML = 'Save Changes';
                        });
                });
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
