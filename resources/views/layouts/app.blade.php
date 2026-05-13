<!DOCTYPE html>
<html lang="en">

@include('layouts.partials.head')

<body class="hold-transition sidebar-mini">

<div class="wrapper">

    @include('layouts.partials.navbar')

    @include('layouts.partials.sidebar')

    <!-- CONTENT -->
    <div class="content-wrapper p-3">
        <div class="mb-2">
            @yield('breadcrumb')
        </div>

        @yield('content')
    </div>

</div>

@include('layouts.partials.scripts')

@stack('scripts')

</body>
</html>
