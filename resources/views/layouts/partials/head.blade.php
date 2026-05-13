<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>POS Amila</title>

    <link rel="stylesheet" href="{{ asset('adminlte/dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        .navbar-nav .nav-link { color: black !important; }
        .main-sidebar { background: #fff; }
        .brand-link { background: #241e1e; color: white !important; }
        .nav-sidebar .nav-link.active { background: #3b82f6; color: white; }
        .content-wrapper { margin-left: 250px; }
    </style>
</head>
