@extends('layouts.card')

@section('content')

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
                            <a href="{{ route('customer.edit', $customer->id) }}" class="btn btn-success">
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

@endsection

@section('paginate')

    <br>

    {{ $customers->links() }}

@endsection
