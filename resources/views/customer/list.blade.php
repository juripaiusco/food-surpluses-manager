@extends('layouts.app')

@section('content')

    <div class="container">

        <div class="card">
            <div class="card-header">{{ __('layout.customer.title') }}</div>
            <div class="card-body">

                <table class="table">

                    <thead>
                    <tr>
                        <th>Utente</th>
                        <th>Indirizzo</th>
                        <th>Codice</th>
                        <th></th>
                    </tr>
                    </thead>

                    <tbody>

                        @foreach($customers as $customer)

                            <tr>
                                <td class="align-middle">{{ $customer->name }} {{ $customer->surname }}</td>
                                <td class="align-middle">{{ $customer->address }}</td>
                                <td class="align-middle">{{ $customer->cod }}</td>
                                <td>

                                    <div class="row no-gutters">
                                        <div class="col">
                                            <a href="#" class="btn btn-success">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                        <div class="col">
                                            <a href="#" class="btn btn-danger">
                                                <i class="far fa-trash-alt"></i>
                                            </a>
                                        </div>
                                    </div>

                                </td>
                            </tr>

                        @endforeach

                    </tbody>


                </table>

            </div>
        </div>

        <br>

        {{ $customers->links() }}

    </div>

@endsection
