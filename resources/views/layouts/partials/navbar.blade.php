<nav class="main-header navbar navbar-expand navbar-white navbar-light border-bottom">

    <!-- LEFT -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                <i class="fas fa-bars" style="font-size:20px;"></i>
            </a>
        </li>
    </ul>

    <!-- BREADCRUMB -->
    <div class="navbar-nav ml-3">
        <div class="d-flex align-items-center font-weight-bold">

            @php
                $breadcrumbs = \App\Helpers\BreadcrumbHelper::generate();
            @endphp

            <div class="d-flex align-items-center">

                @foreach ($breadcrumbs as $index => $crumb)
                    @if ($index > 0)
                        <span class="mx-2 text-muted">></span>
                    @endif

                    @if ($crumb['clickable'])
                        <a href="{{ $crumb['url'] }}" class="{{ $loop->last ? 'text-primary' : 'text-dark' }}"
                            style="font-weight:600; text-decoration:none;">

                            {{ $crumb['name'] }}

                        </a>
                    @else
                        <span class="text-muted font-weight-bold">
                            {{ $crumb['name'] }}
                        </span>
                    @endif
                @endforeach

            </div>
        </div>
    </div>

    <!-- RIGHT -->
    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown">

            <a class="nav-link d-flex align-items-center" data-toggle="dropdown" href="#"
                style="
                    gap:10px;
                    padding:0;
                ">

                <!-- AVATAR -->
                <div
                    style="
                    width:36px;
                    height:36px;
                    border-radius:50%;
                    background:#1f2937;
                    color:white;
                    display:flex;
                    align-items:center;
                    justify-content:center;
                    font-weight:700;
                    font-size:14px;
                ">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>

                <!-- USER -->
                <div class="d-flex flex-column text-left">
                    <span
                        style="
                        font-weight:700;
                        color:#111827;
                        font-size:13px;
                    ">
                        {{ Auth::user()->name }}
                    </span>

                    <small
                        style="
                        color:#6b7280;
                        font-size:10px;
                        font-weight:600;
                    ">
                        {{ ucfirst(str_replace('_', ' ', Auth::user()->role->role_name)) }}
                    </small>
                </div>

                <!-- ICON -->
                <i class="fas fa-chevron-down ml-2"
                    style="
                        font-size:10px;
                        color:#6b7280;
                    ">
                </i>
            </a>

            <!-- DROPDOWN -->
            <div class="dropdown-menu dropdown-menu-right shadow border-0"
                style="
                    border-radius:12px;
                    padding:8px 0;
                    margin-top:10px;
                    min-width:220px;
                ">

                <a href="#" class="dropdown-item py-2" data-toggle="modal" data-target="#changePasswordModal">
                    <i class="fas fa-key mr-2 text-secondary"></i>
                    Ganti Password
                </a>

                <div class="dropdown-divider"></div>

                <form action="/logout" method="POST">
                    @csrf
                    <button type="submit" class="dropdown-item text-danger py-2">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        Logout
                    </button>
                </form>

            </div>
        </li>
    </ul>

</nav>
{{-- modal ganti password --}}
<div class="modal fade" id="changePasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5>Change Password</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fas fa-times-circle text-muted"></i></span>
                </button>
            </div>

            <form method="POST" action="{{ route('change.password') }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Current Password <span class="text-danger">*</span></label>
                        <input type="password" name="current_password" class="form-control" placeholder="Enter current password" required>
                    </div>
                    <div class="form-group">
                        <label>New Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" placeholder="enter new password" required>
                    </div>

                    <div class="form-group">
                        <label>Confirm Password <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm new password" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@if (session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: '{{ session('success') }}'
        })
    </script>
@endif

@if ($errors->any())
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: '{{ $errors->first() }}'
        })
    </script>
@endif
