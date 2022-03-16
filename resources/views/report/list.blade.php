@extends('layouts.card')

@section('card-body')

    @php
        $route_name = current(explode('.', \Illuminate\Support\Facades\Route::currentRouteName()));
    @endphp

    <table class="table">
        <thead>
        <tr>
            <th>Prodotto</th>
            <th class="text-right">Famiglie n.</th>
            <th class="text-right">Componenti n. tot.</th>
        </tr>
        </thead>
        <tbody>
        @foreach($reports as $report)
        <tr>
            <td>
                {{ $report['product']->cod }}
                -
                {{ $report['product']->name }}
            </td>
            <td class="text-right">
                {{ $report['customers_count']['n_family'] }}
            </td>
            <td class="text-right">
                {{ $report['customers_count']['n_family_total'] }}
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>

@endsection

@section('paginate')

    <br>



@endsection
