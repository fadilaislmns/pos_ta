@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <h3 class="mb-3">Movement Detail</h3>

    <div class="card p-3">

        <p><b>Product:</b> {{ $movement->product->product_name }}</p>
        <p><b>Type:</b> {{ $movement->movement_type }}</p>
        <p><b>Qty:</b> {{ $movement->quantity }}</p>
        <p><b>Source:</b> {{ $movement->source }}</p>
        <p><b>Date:</b> {{ $movement->created_at->format('d-m-Y') }}</p>

    </div>

</div>
@endsection
