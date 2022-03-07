@extends('layouts.card')

@section('card-body')

    <table class="table table-info">
        <thead>
        <tr>
            <th class="w-25">Riferimento</th>
            <th class="w-25">Data</th>
            <th class="w-25">Utente</th>
            <th class="w-25">Cliente</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>
                {{ $order->reference }}
            </td>
            <td>
                {{ $order->date }}
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
