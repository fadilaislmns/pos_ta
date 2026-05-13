<nav class="main-header navbar navbar-expand navbar-white navbar-light">

    @php
        $breadcrumbs = \App\Helpers\BreadcrumbHelper::generate();
    @endphp

    <div class="ml-3 font-weight-bold">
        @foreach ($breadcrumbs as $key => $crumb)
            @if ($key != count($breadcrumbs) - 1)
                <a href="{{ $crumb['url'] }}">{{ $crumb['name'] }}</a> >
            @else
                <span>{{ $crumb['name'] }}</span>
            @endif
        @endforeach
    </div>

    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown">
            <a class="nav-link d-flex align-items-center" data-toggle="dropdown" href="#">
                <div style="width:30px;height:30px;border-radius:50%;background:#3b82f6;color:white;display:flex;align-items:center;justify-content:center;">
                    U
                </div>
                <span>User</span>
            </a>

            <div class="dropdown-menu dropdown-menu-right">
                <a href="#" class="dropdown-item">Profile</a>
                <a href="#" class="dropdown-item">Ganti Password</a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item text-danger">Logout</a>
            </div>
        </li>
    </ul>

</nav>
