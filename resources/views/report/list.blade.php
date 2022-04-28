@extends('layouts.card')

@section('card-body')

    @php
        $route_name = current(explode('.', \Illuminate\Support\Facades\Route::currentRouteName()));
    @endphp

    <div class="row">
        <div class="col">

            <div class="h2">Report del @if(isset($s)) {{ $s }} @else {{ date('d/m/Y') }} @endif</div>

        </div>
        <div class="col">

            <div class="float-right">
                <form class="form-inline my-2 my-lg-0" action="{{ route('report') }}" method="get">

                    <input class="form-control mr-sm-2"
                           type="search"
                           placeholder="cerca {{ __('layout.' . $route_name . '.single') }}"
                           aria-label="Search"
                           name="s"
                           value="{{ $s ?? '' }}" />

                    <button class="btn btn-outline-info my-2 my-sm-0" type="submit">Cerca</button>

                </form>
            </div>

        </div>
    </div>

    @if($s)
    <div class="row">
        <div class="col text-right">
            <a href="{{ route('report.mailsend') }}?s={{ $s }}" class="btn btn-success">Invia report via mail</a>
        </div>
    </div>
    @endif

    <br>

    <table class="table table-striped table-hover">
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
