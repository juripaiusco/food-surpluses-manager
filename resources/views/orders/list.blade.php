@extends('layouts.card')

@section('card-body')

    @php
        $route_name = current(explode('.', \Illuminate\Support\Facades\Route::currentRouteName()));
    @endphp

    <div class="row">
        <div class="col">

            <div class="float-right">
                <form class="form-inline my-2 my-lg-0" action="{{ route('products') }}" method="get">

                    <input class="form-control mr-sm-2"
                           type="search"
                           placeholder="cerca ordine"
                           aria-label="Search"
                           name="s"
                           value="{{ $s ?? '' }}" />

                    <button class="btn btn-outline-info my-2 my-sm-0" type="submit">Cerca</button>

                </form>
            </div>

        </div>
    </div>

    <br>

    <table class="table table-striped table-hover">

        <thead>
        <tr>
            <th width="10%" class="text-center">Data</th>
            <th width="10%" class="text-center">Riferimento</th>
            <th>Negozio</th>
            <th>Cliente</th>
            <th width="10%" class="text-right">Punti</th>
            <th width="1%"></th>
        </tr>
        </thead>

        <tbody>

        @foreach($orders as $order)

            <tr>
                <td class="align-middle text-center">
                    <small>
                        {{ date('d/m/Y', strtotime($order->date)) }}
                        <br>
                        {{ date('H:i:s', strtotime($order->date)) }}
                    </small>
                </td>
                <td class="align-middle text-center">{{ $order->reference }}</td>
                <td class="align-middle">{{ $order->retail->name }}</td>
                <td class="align-middle">
                    {{ $order->customer->cod }}
                    -
                    {{ $order->customer->name }} {{ $order->customer->surname }}
                </td>
                <td class="align-middle text-right">{{ $order->points }}</td>
                <td class="text-center">

                    <a href="{{ route('orders.show', $order->id) }}" class="btn btn-primary">
                        <i class="fas fa-info"></i>
                    </a>

                </td>
            </tr>

        @endforeach

        </tbody>


    </table>

@endsection

@section('paginate')

    <br>

    {{ $orders->links() }}

@endsection
