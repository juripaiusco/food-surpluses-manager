@extends('layouts.card')

@section('card-body')

    <div class="row">
        <div class="col-9">

            <h2>Dati cliente</h2>
            <table class="table table-info">
                <thead>
                <tr>
                    <th>Data</th>
                    <th>Riferimento</th>
                    <th>Negozio</th>
                    <th>Utente</th>
                    <th>Cliente</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>
                        {{ $order->date }}
                    </td>
                    <td>
                        {{ $order->reference }}
                    </td>
                    <td>
                        {{ $order->retail->name }}
                    </td>
                    <td>
                        {{ $order->user->name }}
                    </td>
                    <td>
                        {{ $order->customer->name }} {{ $order->customer->surname }}
                    </td>
                </tr>
                </tbody>
            </table>

        </div>
        <div class="col text-center">

            <h2>Punti scalati</h2>

            <br>

            <span class="h1">
                {{ $order->points * (-1) }}
            </span>

        </div>
    </div>

    <br>

    <h2>Prodotti consegnati</h2>

    <table class="table table-striped table-hover table-warning">
        <thead>
        <tr>
            <th class="w-25">Codice</th>
            <th class="w-25">Tipo</th>
            <th class="w-25">Nome</th>
            <th class="w-25 text-right">Punti</th>
        </tr>
        </thead>
        <tbody>
        @foreach($products as $product)

            <tr>
                <td>{{ $product->cod }}</td>
                <td>{{ $product->type == 'fead no' ? '' : $product->type }}</td>
                <td>{{ $product->name }}</td>
                <td class="text-right">{{ $product->points }}</td>
            </tr>

        @endforeach
        </tbody>
    </table>

@endsection
