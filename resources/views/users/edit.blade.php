@extends('layouts.app')

@section('content')
    <div class="container-fluid mb-5">

        <div class="mb-4">
            <h4 class="font-weight-bold mb-0">Edit User</h4>
        </div>

        <form id="formEditUser" autocomplete="off">
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <div class="card-body p-4">

                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark">Nama <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control" placeholder="Masukkan nama"
                            autocomplete="off" value="{{ $user->name }}" required>
                        <div class="invalid-feedback font-weight-bold" id="error-name"></div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="email" class="form-control"
                            placeholder="Masukkan email" autocomplete="off" value="{{ $user->email }}" required>
                        <div class="invalid-feedback font-weight-bold" id="error-email"></div>
                    </div>

                    <div class="form-group mb-0">
                        <label class="font-weight-bold text-dark">Role <span class="text-danger">*</span></label>
                        <select name="role_id" id="role_id" class="form-control" required>
                            <option value="" disabled>Pilih role user</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $role->role_name)) }}
                                </option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback font-weight-bold" id="error-role_id"></div>
                    </div>

                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <div class="card-body p-4">

                    <div class="form-group mb-0">
                        <label class="font-weight-bold text-dark d-block">Status</label>
                        <div class="d-flex align-items-center" style="gap: 12px;">
                            <div class="custom-switch-btn {{ $user->status == 'active' ? 'active' : '' }}" id="statusSwitch"
                                style="cursor: pointer;">
                                <div class="custom-switch-thumb"></div>
                            </div>
                            <span class="status-text" id="statusLabel">{{ ucfirst($user->status) }}</span>
                            <input type="hidden" name="status" id="statusInput" value="{{ $user->status }}">
                        </div>
                    </div>

                </div>
            </div>

            <div class="d-flex justify-content-end" style="gap: 12px;">
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary px-4 font-weight-bold"
                    style="border-radius: 6px;">Batal</a>
                <button type="submit" id="btnSubmit" class="btn btn-success px-4 font-weight-bold"
                    style="background-color: #4CAF50; border: none; border-radius: 6px;">Simpan Perubahan</button>
            </div>
        </form>

    </div>


    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                // 1. FITUR TOGGLE STATUS
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

                // 2. FITUR CEK EMAIL REAL-TIME
                const emailInput = document.getElementById('email');
                const emailError = document.getElementById('error-email');
                const originalEmail = "{{ $user->email }}";

                emailInput.addEventListener('blur', function() {
                    const emailValue = this.value.trim();

                    if (emailValue !== '' && emailValue !== originalEmail) {
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

                // 3. FITUR SUBMIT AJAX
                const formEditUser = document.getElementById('formEditUser');
                const btnSubmit = document.getElementById('btnSubmit');
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                formEditUser.addEventListener('submit', function(e) {
                    e.preventDefault();

                    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                    document.querySelectorAll('.invalid-feedback').forEach(el => el.innerText = '');

                    const originalBtnText = btnSubmit.innerHTML;
                    btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...';
                    btnSubmit.disabled = true;

                    const formData = {
                        name: document.getElementById('name').value,
                        email: emailInput.value,
                        role_id: document.getElementById('role_id').value,
                        status: statusInput.value
                    };

                    fetch("{{ route('users.update', $user->id) }}", {
                            method: 'PUT',
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
                                if (response.status === 422) {
                                    for (let field in data.errors) {
                                        let inputElement = document.getElementById(field);
                                        let errorElement = document.getElementById('error-' + field);

                                        if (inputElement && errorElement) {
                                            inputElement.classList.add('is-invalid');
                                            errorElement.innerText = data.errors[field][0];
                                        }
                                    }
                                    throw new Error('VALIDATION_ERROR');
                                }
                                throw new Error(data.message || 'Gagal update user');
                            }
                            return data;
                        })
                        .then(data => {
                            window.location.href = "{{ route('users.index') }}";
                        })
                        .catch(error => {
                            if (error.message !== 'VALIDATION_ERROR') {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: error.message
                                });
                            }
                        })
                        .finally(() => {
                            btnSubmit.innerHTML = originalBtnText;
                            btnSubmit.disabled = false;
                        });
                });

            });
        </script>
    @endpush
@endsection
