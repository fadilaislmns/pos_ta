@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <!-- TITLE -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2 class="font-weight-bold">

            Detail Stock

        </h2>

        <a href="/stocks" class="btn btn-light border">

            <i class="fas fa-arrow-left mr-1"></i>

            Kembali

        </a>

    </div>

    <!-- PRODUCT INFO -->
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body">

            <div class="row">

                <!-- PRODUCT -->
                <div class="col-md-4">

                    <small class="text-muted">

                        Produk

                    </small>

                    <h4 class="font-weight-bold">

                        {{ $stock->product->product_name }}

                    </h4>

                </div>

                <!-- STATUS -->
                <div class="col-md-4">

                    <small class="text-muted">

                        Status

                    </small>

                    <div class="mt-1">

                        @if ($stock->current_stock <= $stock->reorder_point)

                            <span class="badge badge-danger">

                                Perlu Restock

                            </span>

                        @else

                            <span class="badge badge-success">

                                Aman

                            </span>

                        @endif

                    </div>

                </div>

                <!-- LAST MOVEMENT -->
                <div class="col-md-4">

                    <small class="text-muted">

                        Last Movement

                    </small>

                    <div class="font-weight-bold">

                        @if ($stock->latestMovement)

                            {{ $stock->latestMovement->created_at->format('d/m/Y H:i') }}

                        @else

                            -

                        @endif

                    </div>

                </div>

            </div>

        </div>

    </div>

    <!-- SUMMARY -->
    <div class="row mb-4">

        <!-- CURRENT STOCK -->
        <div class="col-md-4">

            <div class="card border-0 shadow-sm">

                <div class="card-body">

                    <small class="text-muted">

                        Stock Saat Ini

                    </small>

                    <h3 class="font-weight-bold">

                        {{ $stock->current_stock }}

                    </h3>

                </div>

            </div>

        </div>

        <!-- SAFETY -->
        <div class="col-md-4">

            <div class="card border-0 shadow-sm">

                <div class="card-body">

                    <small class="text-muted">

                        Safety Stock

                    </small>

                    <h3 class="font-weight-bold">

                        {{ $stock->safety_stock }}

                    </h3>

                </div>

            </div>

        </div>

        <!-- ROP -->
        <div class="col-md-4">

            <div class="card border-0 shadow-sm">

                <div class="card-body">

                    <small class="text-muted">

                        Reorder Point

                    </small>

                    <h3 class="font-weight-bold">

                        {{ $stock->reorder_point }}

                    </h3>

                </div>

            </div>

        </div>

    </div>

    <!-- ANALYSIS -->
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body">

            <h5 class="font-weight-bold mb-3">

                Analisis Stock

            </h5>

            @if ($stock->current_stock <= $stock->reorder_point)

                <div class="alert alert-danger mb-0">

                    Stock produk sudah mencapai reorder point.
                    Disarankan segera melakukan restock.

                </div>

            @else

                <div class="alert alert-success mb-0">

                    Stock produk masih dalam kondisi aman.

                </div>

            @endif

        </div>

    </div>

    <!-- MOVEMENT HISTORY -->
    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white border-0">

            <h5 class="mb-0 font-weight-bold">

                Riwayat Stock Movement

            </h5>

        </div>

        <div class="card-body p-0">

            <table class="table table-hover mb-0">

                <thead>

                    <tr>

                        <th>Tanggal</th>
                        <th>Type</th>
                        <th>Qty</th>
                        <th>Source</th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($movements as $m)

                        <tr>

                            <td>

                                {{ $m->created_at->format('d/m/Y H:i') }}

                            </td>

                            <td>

                                @if($m->movement_type == 'in')

                                    <span class="badge badge-success">

                                        IN

                                    </span>

                                @else

                                    <span class="badge badge-danger">

                                        OUT

                                    </span>

                                @endif

                            </td>

                            <td>

                                {{ $m->quantity }}

                            </td>

                            <td>

                                {{ $m->source }}

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="4" class="text-center py-4">

                                Belum ada movement

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection
