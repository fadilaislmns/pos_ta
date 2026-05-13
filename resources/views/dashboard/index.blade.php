@extends('layouts.app')

@section('content')
    <h3>Dashboard</h3>

    <div class="card">
        <div class="card-body">
            Selamat datang di sistem POS
        </div>
    </div>
    <script>
        if (!localStorage.getItem("token")) {
            window.location.href = "/";
        }
    </script>
    <script>
        fetch("http://127.0.0.1:8000/api/products", {
                headers: {
                    "Authorization": "Bearer " + localStorage.getItem("token")
                }
            })
            .then(res => res.json())
            .then(data => console.log(data));
    </script>
@endsection
