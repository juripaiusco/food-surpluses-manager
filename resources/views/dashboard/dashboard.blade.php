@extends('layouts.card')

@section('card-body')

    @php
        $route_name = current(explode('.', \Illuminate\Support\Facades\Route::currentRouteName()));
    @endphp

    <div class="row">

        <div class="col">

            <div class="card border-warning">
                <div class="card-body">

                    <div class="row">
                        <div class="col text-center">

                            <span class="h1 card-title">
                                {{ $products }}
                            </span>
                            <div class="card-text">Prodotti</div>

                        </div>
                        <div class="col">

                            <i class="fas fa-list fa-4x"></i>

                        </div>
                    </div>

                </div>
            </div>

        </div>

        <div class="col">

            <div class="card border-info">
                <div class="card-body">

                    <div class="row">
                        <div class="col text-center">

                            <span class="h1 card-title">
                                {{ $customers }}
                            </span>
                            <div class="card-text">Tessere</div>

                        </div>
                        <div class="col">

                            <i class="fas fa-users fa-4x"></i>

                        </div>
                    </div>

                </div>
            </div>

        </div>

        <div class="col">

            <div class="card border-secondary">
                <div class="card-body">

                    <div class="row">
                        <div class="col text-center">

                            <span class="h1 card-title">
                                {{ $orders }}
                            </span>
                            <div class="card-text">Ordini</div>

                        </div>
                        <div class="col">

                            <i class="fas fa-shopping-bag fa-4x"></i>

                        </div>
                    </div>

                </div>
            </div>


        </div>

        <div class="col">

            <div class="card border-success">
                <div class="card-body">

                    <div class="row">
                        <div class="col text-center">

                            <span class="h1 card-title">
                                {{ $points }}
                            </span>
                            <div class="card-text">Punti</div>

                        </div>
                        <div class="col">

                            <i class="fas fa-bullseye fa-4x"></i>

                        </div>
                    </div>

                </div>
            </div>

        </div>

    </div>

    <br>

    <div class="row">
        <div class="col-12">

            <div class="card">
                <div class="card-header">

                    Ordini di oggi

                </div>
                <div class="card-body">

                    <table class="table table-stripped table-hover table-borderless">
                        <thead>
                        <tr>
                            <th>Data</th>
                            <th>Rif.</th>
                            <th>Cliente</th>
                            <th class="text-right">Punti</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($orders_today as $order)
                            <tr>
                                <td class="align-middle">
                                    <small>
                                        {{ date('d/m/Y', strtotime($order->date)) }}
                                        <br>
                                        {{ date('H:i:s', strtotime($order->date)) }}
                                    </small>
                                </td>
                                <td class="align-middle">
                                    {{ $order->reference }}
                                </td>
                                <td class="align-middle">
                                    {{ $order->customer->cod }}
                                    -
                                    {{ $order->customer->name }} {{ $order->customer->surname }}
                                </td>
                                <td class="align-middle text-right">
                                    {{ $order->points }}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                </div>
            </div>

        </div>
        {{--<div class="col">

            <div class="card">
                <div class="card-header">

                    Tessere di oggi

                </div>
                <div class="card-body">

                    lista

                </div>
            </div>

        </div>--}}
    </div>

@endsection
