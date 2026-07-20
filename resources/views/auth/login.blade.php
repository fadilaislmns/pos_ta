<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Amila POS</title>
    <!-- ADMINLTE -->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/dist/css/adminlte.min.css') }}">

    <style>
        body {
            background: #f3f4f6;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Poppins', sans-serif;
        }

        .login-wrapper {
            width: 850px;
            background: white;
            border-radius: 18px;
            padding: 50px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, .05);
        }
        .login-left {
            border-right: 1px solid #e5e7eb;
        }
        .logo-box {
            width: 90px;
            height: 90px;
            background: #f3f4f6;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 14px;
            margin: auto;
            margin-bottom: 20px;
        }
        .logo-box i {
            font-size: 38px;
            color: #2563eb;
        }
        .brand-title {
            font-size: 32px;
            font-weight: 700;
            color: #1e3a8a;
        }
        .brand-subtitle {
            color: #6b7280;
        }
        .login-title {
            font-weight: 700;
            color: #1e3a8a;
        }
        .form-control {
            border-radius: 10px;
            height: 45px;
        }
        .btn-login {
            height: 45px;
            border-radius: 10px;
            font-weight: 600;
        }
    </style>

</head>

<body>
    <div class="login-wrapper">
        <div class="row align-items-center">
            <!-- LEFT -->
            <div class="col-md-5 text-center login-left">
                <div class="logo-box">
                    <i class="fas fa-cash-register"></i>
                </div>

                <h2 class="brand-title">
                    Amila POS
                </h2>

                <p class="brand-subtitle">
                    Point of Sale & Inventory System
                </p>
            </div>

            <!-- RIGHT -->
            <div class="col-md-7 pl-5">
                <h3 class="login-title mb-2">Welcome Back</h3>
                <p class="text-muted mb-4">Login to continue</p>

                <!-- ERROR -->
                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                <form action="/login" method="POST">
                    @csrf
                    <!-- EMAIL -->
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" placeholder="example@gmail.com"
                            required>
                    </div>

                    <!-- PASSWORD -->
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" placeholder="********" required>
                    </div>

                    <!-- BUTTON -->
                    <button class="btn btn-primary btn-block btn-login">Login</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
