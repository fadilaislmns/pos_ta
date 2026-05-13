<!DOCTYPE html>

<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="{{ asset('adminlte/dist/css/adminlte.min.css') }}">
</head>

<body class="hold-transition login-page">

<div class="login-box">
    <div class="card">
        <div class="card-body login-card-body">


        <h4 class="text-center">Login POS</h4>

        <form id="loginForm">
            <div class="input-group mb-3">
                <input type="email" id="email" class="form-control" placeholder="Email">
            </div>

            <div class="input-group mb-3">
                <input type="password" id="password" class="form-control" placeholder="Password">
            </div>

            <button type="submit" class="btn btn-primary btn-block">Login</button>
        </form>

        <p id="error" class="text-danger mt-2"></p>

    </div>
</div>


</div>

<script>
document.getElementById('loginForm').addEventListener('submit', function(e){
    e.preventDefault();

    fetch("http://127.0.0.1:8000/api/auth/login", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            email: document.getElementById('email').value,
            password: document.getElementById('password').value
        })
    })
    .then(res => res.json())
    .then(data => {
        if(data.success){
            localStorage.setItem("token", data.token);
            window.location.href = "/dashboard";
        } else {
            document.getElementById("error").innerText = data.message;
        }
    });
});
</script>

</body>
</html>
