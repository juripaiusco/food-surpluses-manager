@extends('layouts.card')

@section('card-body')

    @php
        $route_name = current(explode('.', \Illuminate\Support\Facades\Route::currentRouteName()));
    @endphp

    @include('js.modalDelete')

    <div class="row">
        <div class="col-lg-8">
            <a class="btn btn-primary" href="{{ route('retails.create') }}">Nuovo negozio</a>
        </div>
        <div class="col-lg-4">

            <div class="float-right">
                <form class="form-inline my-2 my-lg-0" action="{{ route('retails') }}" method="get">

                    <input class="form-control mr-sm-2"
                           type="search"
                           placeholder="cerca negozio"
                           aria-label="Search"
                           name="s"
                           value="{{ $s ?? '' }}" />

                    <button class="btn btn-outline-info my-2 my-sm-0" type="submit">Cerca</button>

                </form>
            </div>

        </div>
    </div>

    <br>

    <table class="table">

        <thead>
        <tr>
            <th>Nome</th>
            <th>Indirizzo</th>
            <th width="120px"></th>
        </tr>
        </thead>

        <tbody>

        @foreach($retails as $retail)

            <tr>
                <td class="align-middle">{{ $retail->name }}</td>
                <td class="align-middle">
                    {{ $retail->address }}
                    -
                    {{ $retail->zip }}
                    -
                    {{ $retail->city }}
                </td>
                <td class="text-center">

                    <div class="row no-gutters">
                        <div class="col">
                            <a href="{{ route('retails.edit', $retail->id) }}"
                               class="btn btn-success">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                        <div class="col">

                            <button type="button"
                                    class="btn btn-danger"
                                    data-toggle="modal"
                                    data-target="#deleteModal"
                                    data-href="{{ route('retails.destroy', $retail->id) }}">
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

    {{ $retails->links() }}

@endsection
