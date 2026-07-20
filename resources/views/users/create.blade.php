@extends('layouts.app')

@section('content')
    <div class="container-fluid mb-5">

        <div class="mb-4">
            <h4 class="font-weight-bold mb-0">Tambah User</h4>
        </div>

        <form id="formAddUser" autocomplete="off">
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <div class="card-body p-4">

                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark">Nama <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control" placeholder="Masukkan nama"
                            autocomplete="off" required>
                        <div class="invalid-feedback font-weight-bold" id="error-name"></div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="email" class="form-control"
                            placeholder="Masukkan email" autocomplete="off" required>
                        <div class="invalid-feedback font-weight-bold" id="error-email"></div>
                    </div>

                    <div class="form-group mb-0">
                        <label class="font-weight-bold text-dark">Role <span class="text-danger">*</span></label>
                        <select name="role_id" id="role_id" class="form-control" required>
                            <option value="" disabled selected>Pilih role user</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}">{{ ucfirst(str_replace('_', ' ', $role->role_name)) }}
                                </option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback font-weight-bold" id="error-role_id"></div>
                    </div>

                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <div class="card-body p-4">

                    <div class="form-group mb-3 position-relative">
                        <label class="font-weight-bold text-dark">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" id="password" class="form-control pr-5"
                            placeholder="Masukkan password" autocomplete="new-password" required>
                        <i class="fas fa-eye toggle-password text-muted" toggle="#password"></i>
                        <div class="invalid-feedback font-weight-bold" id="error-password"></div>
                    </div>

                    <div class="form-group mb-3 position-relative">
                        <label class="font-weight-bold text-dark">Konfirmasi Password <span
                                class="text-danger">*</span></label>
                        <input type="password" id="password_confirmation" class="form-control pr-5"
                            placeholder="Konfirmasi password" required>
                        <i class="fas fa-eye toggle-password text-muted" toggle="#password_confirmation"></i>
                    </div>

                    <div class="form-group mb-0">
                        <label class="font-weight-bold text-dark d-block">Status</label>
                        <div class="d-flex align-items-center" style="gap: 12px;">
                            <div class="custom-switch-btn active" id="statusSwitch" style="cursor: pointer;">
                                <div class="custom-switch-thumb"></div>
                            </div>
                            <span class="status-text" id="statusLabel">Active</span>
                            <input type="hidden" name="status" id="statusInput" value="active">
                        </div>
                    </div>

                </div>
            </div>

            <div class="d-flex justify-content-end" style="gap: 12px;">
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary px-4 font-weight-bold"
                    style="border-radius: 6px;">Batal</a>
                <button type="submit" id="btnSubmit" class="btn btn-success px-4 font-weight-bold"
                    style="background-color: #4CAF50; border: none; border-radius: 6px;">Simpan</button>
            </div>
        </form>

    </div>


    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                // 1. FITUR TOGGLE SHOW/HIDE PASSWORD
                const togglePasswordIcons = document.querySelectorAll('.toggle-password');
                togglePasswordIcons.forEach(icon => {
                    icon.addEventListener('click', function() {
                        const input = document.querySelector(this.getAttribute('toggle'));
                        if (input.type === 'password') {
                            input.type = 'text';
                            this.classList.remove('fa-eye');
                            this.classList.add('fa-eye-slash');
                        } else {
                            input.type = 'password';
                            this.classList.remove('fa-eye-slash');
                            this.classList.add('fa-eye');
                        }
                    });
                });

                // 2. FITUR TOGGLE STATUS
                const statusSwitch = document.getElementById('statusSwitch');
                const statusInput = document.getElementById('statusInput');
                const statusLabel = document.getElementById('statusLabel');

                statusSwitch.addEventListener('click', function() {
                    this.classList.toggle('active');
                    if (this.classList.contains('active')) {
                        statusInput.value = 'active';
                        statusLabel.innerText = 'Active';
                    } else {
                        statusInput.value = 'inactive';
                        statusLabel.innerText = 'Inactive';
                    }
                });

                // 3. FITUR SUBMIT AJAX
                const formAddUser = document.getElementById('formAddUser');
                const btnSubmit = document.getElementById('btnSubmit');
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                formAddUser.addEventListener('submit', function(e) {
                    e.preventDefault(); // Mencegah reload halaman

                    // 1. BERSIHKAN ERROR SEBELUMNYA
                    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                    document.querySelectorAll('.invalid-feedback').forEach(el => el.innerText = '');

                    // 2. VALIDASI KONFIRMASI PASSWORD DI CLIENT
                    const password = document.getElementById('password');
                    const confirmPassword = document.getElementById('password_confirmation');

                    if (password.value !== confirmPassword.value) {
                        confirmPassword.classList.add('is-invalid');
                        // Kita bisa langsung memunculkan pesan merah di bawah form konfirmasi
                        // Pastikan kamu punya id="error-password_confirmation" di html nya
                        Swal.fire('Oops...', 'Password dan Konfirmasi Password tidak cocok!', 'warning');
                        return;
                    }

                    // Ubah text tombol saat loading
                    const originalBtnText = btnSubmit.innerHTML;
                    btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...';
                    btnSubmit.disabled = true;

                    // Ambil data form
                    const formData = {
                        name: document.getElementById('name').value,
                        email: document.getElementById('email').value,
                        role_id: document.getElementById('role_id').value,
                        password: password.value,
                        status: statusInput.value
                    };

                    // Hit Controller Store
                    fetch("{{ route('users.store') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(formData)
                        })
                        .then(async response => {
                            const data = await response.json();

                            if (!response.ok) {
                                // Jika ada error validasi dari Laravel (Status 422 - Email sudah ada, dll)
                                if (response.status === 422) {
                                    // Loop semua error dari backend
                                    for (let field in data.errors) {
                                        let inputElement = document.getElementById(field);
                                        let errorElement = document.getElementById('error-' + field);

                                        if (inputElement && errorElement) {
                                            inputElement.classList.add(
                                                'is-invalid'); // Bikin border kotak jadi merah
                                            errorElement.innerText = data.errors[field][
                                                0
                                            ]; // Isi teks merah di bawahnya
                                        }
                                    }
                                    throw new Error(
                                        'VALIDATION_ERROR'
                                    ); // Hentikan eksekusi, jgn panggil SweetAlert
                                }
                                // Error lain misal database mati (Status 500)
                                throw new Error(data.message || 'Gagal menambahkan user');
                            }
                            return data;
                        })
                        .then(data => {
                            // SUKSES ALERT
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'User baru berhasil ditambahkan ke sistem.',
                                showConfirmButton: false,
                                timer: 2000
                            }).then(() => {
                                // Redirect kembali ke halaman tabel setelah sukses
                                window.location.href = "{{ route('users.index') }}";
                            });
                        })
                        .catch(error => {
                            // Hanya panggil pop-up SweetAlert jika errornya BUKAN dari validasi form
                            if (error.message !== 'VALIDATION_ERROR') {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: error.message
                                });
                            }
                        })
                        .finally(() => {
                            // Kembalikan tombol ke kondisi semula
                            btnSubmit.innerHTML = originalBtnText;
                            btnSubmit.disabled = false;
                        });
                });

                // FITUR CEK EMAIL
                const emailInput = document.getElementById('email');
                const emailError = document.getElementById('error-email');

                emailInput.addEventListener('blur', function() {
                    const emailValue = this.value.trim();

                    if (emailValue !== '') {
                        fetch("{{ route('users.check-email') }}", {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                        .getAttribute('content')
                                },
                                body: JSON.stringify({
                                    email: emailValue
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.status === 'error') {
                                    emailInput.classList.add('is-invalid');
                                    emailError.innerText = data.message;
                                } else {
                                    emailInput.classList.remove('is-invalid');
                                    emailError.innerText = '';
                                }
                            })
                            .catch(error => console.error('Error:', error));
                    } else {
                        emailInput.classList.remove('is-invalid');
                        emailError.innerText = '';
                    }
                });

                // FITUR CEK PASSWORD 
                const confirmPasswordInput = document.getElementById('password_confirmation');
                const passwordInput = document.getElementById('password');
                const confirmPasswordError = document.getElementById('error-password_confirmation');

                confirmPasswordInput.addEventListener('input', function() {
                    if (this.value !== passwordInput.value) {
                        this.classList.add('is-invalid');
                        confirmPasswordError.innerText = 'Password tidak cocok.';
                    } else {
                        this.classList.remove('is-invalid');
                        confirmPasswordError.innerText = '';
                    }
                });

            });
        </script>
    @endpush
@endsection
