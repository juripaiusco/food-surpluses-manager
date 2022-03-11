@extends('layouts.card')

@section('card-body')

    @include('js.modalDelete')

    <div class="row">
        <div class="col-lg-8">
            <a class="btn btn-primary" href="{{ route('customers.create') }}">Nuova anagrafica</a>
        </div>
        <div class="col-lg-4">

            <div class="float-right">
                <form class="form-inline my-2 my-lg-0" action="{{ route('customers') }}" method="get">

                    <input class="form-control mr-sm-2"
                           type="search"
                           placeholder="cerca anagrafica"
                           aria-label="Search"
                           name="s"
                           value="{{ $s ?? '' }}" />

                    <button class="btn btn-outline-info my-2 my-sm-0" type="submit">Cerca</button>

                </form>
            </div>

        </div>
    </div>

    <br>

    <table class="table table-hover table-striped">

        <thead>
        <tr>
            <th>Codice</th>
            <th>Utente</th>
            <th>Indirizzo</th>
            <th class="text-right">Componenti</th>
            <th class="text-center">Punti</th>
            <th width="120px"></th>
        </tr>
        </thead>

        <tbody>

        @foreach($customers as $customer)

            <tr>
                <td class="align-middle">{{ $customer->cod }}</td>
                <td class="align-middle">{{ $customer->name }} {{ $customer->surname }}</td>
                <td class="align-middle">{{ $customer->address }}</td>
                <td class="align-middle text-right">{{ $customer->family_number }}</td>
                <td class="align-middle text-center">
                    <small>
                        {{ $customer->points }}&nbsp;/&nbsp;{{ $customer->points_renew }}
                    </small>

                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar" role="progressbar" style="width: {{ $customer->points / $customer->points_renew * 100 }}%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>

                </td>
                <td class="text-center">

                    <div class="row no-gutters">
                        <div class="col">
                            <a href="{{ route('customers.edit', $customer->id) }}"
                               class="btn btn-success">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                        <div class="col">

                            <button type="button"
                                    class="btn btn-danger"
                                    data-toggle="modal"
                                    data-target="#deleteModal"
                                    data-href="{{ route('customers.destroy', $customer->id) }}">
                                <i class="far fa-trash-alt"></i>
                            </button>

                        </div>
                    </div>

                </td>
            </tr>

        @endforeach

        </tbody>


    </table>

@endsection

@section('paginate')

    <br>

    {{ $customers->links() }}

@endsection
