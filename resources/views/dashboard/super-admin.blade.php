@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <!-- HEADER -->
        <div class="mb-4">
            <h3 class="font-weight-bold mb-1">
                Dashboard Super Admin
            </h3>

            <small class="text-muted">
                Monitoring user dan akses sistem
            </small>
        </div>

        <!-- CARD -->
        <div class="row">

            <!-- TOTAL USER -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm dashboard-card">
                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-start">

                            <div>
                                <small class="text-muted">
                                    Total User
                                </small>

                                <h3 class="font-weight-bold mt-2 mb-0">
                                    {{ $totalUsers }}
                                </h3>
                            </div>

                            <div class="dashboard-icon bg-dark">
                                <i class="fas fa-users"></i>
                            </div>

                        </div>

                    </div>
                </div>
            </div>

            <!-- USER ACTIVE -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm dashboard-card">
                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-start">

                            <div>
                                <small class="text-muted">
                                    User Active
                                </small>

                                <h3 class="font-weight-bold mt-2 mb-0">
                                    {{ $activeUsers }}
                                </h3>
                            </div>

                            <div class="dashboard-icon bg-success">
                                <i class="fas fa-user-check"></i>
                            </div>

                        </div>

                    </div>
                </div>
            </div>

            <!-- USER INACTIVE -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm dashboard-card">
                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-start">

                            <div>
                                <small class="text-muted">
                                    User Inactive
                                </small>

                                <h3 class="font-weight-bold mt-2 mb-0">
                                    {{ $inactiveUsers }}
                                </h3>
                            </div>

                            <div class="dashboard-icon bg-danger">
                                <i class="fas fa-user-slash"></i>
                            </div>

                        </div>

                    </div>
                </div>
            </div>

            <!-- TOTAL ROLE -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm dashboard-card">
                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-start">

                            <div>
                                <small class="text-muted">
                                    Total Role
                                </small>

                                <h3 class="font-weight-bold mt-2 mb-0">
                                    {{ $totalRoles }}
                                </h3>
                            </div>

                            <div class="dashboard-icon bg-primary">
                                <i class="fas fa-user-shield"></i>
                            </div>

                        </div>

                    </div>
                </div>
            </div>

        </div>

        <!-- TABLE USER -->
        <div class="card border-0 shadow-sm"
            style="border-radius:20px;">

            <div class="card-body">

                <!-- HEADER -->
                <div class="d-flex justify-content-between align-items-center mb-4">

                    <div>
                        <h5 class="font-weight-bold mb-1">
                            User Terbaru
                        </h5>

                        <small class="text-muted">
                            Monitoring pengguna sistem
                        </small>
                    </div>

                    <a href="{{ route('users.index') }}"
                        class="btn btn-dark">

                        Lihat Semua

                    </a>

                </div>

                <!-- TABLE -->
                <div class="table-responsive">

                    <table class="table align-middle">

                        <thead>
                            <tr>

                                <th>Nama</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Dibuat</th>

                            </tr>
                        </thead>

                        <tbody>

                            @forelse ($latestUsers as $user)
                                <tr>

                                    <td>
                                        {{ $user->name }}
                                    </td>

                                    <td>
                                        {{ $user->email }}
                                    </td>

                                    <td>
                                        {{ ucfirst(str_replace('_', ' ', $user->role->role_name)) }}
                                    </td>

                                    <td>

                                        @if ($user->status == 'active')
                                            <span class="status-active">
                                                Active
                                            </span>
                                        @else
                                            <span class="status-inactive">
                                                Inactive
                                            </span>
                                        @endif

                                    </td>

                                    <td>
                                        {{ $user->created_at->format('d M Y') }}
                                    </td>

                                </tr>
                            @empty

                                <tr>
                                    <td colspan="5"
                                        class="text-center text-muted py-4">

                                        Tidak ada data user

                                    </td>
                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>
        </div>

    </div>
@endsection
