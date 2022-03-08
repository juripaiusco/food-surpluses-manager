@extends('layouts.card')

@section('card-body')

    @include('js.modalDelete')

    <div class="row">
        <div class="col-lg-8">
            {{--<a class="btn btn-primary" href="{{ route('products.create') }}">Nuovo prodotto</a>--}}
        </div>
        <div class="col-lg-4">

            <div class="float-right">
                <form class="form-inline my-2 my-lg-0" action="{{ route('products') }}" method="get">

                    <input class="form-control mr-sm-2"
                           type="search"
                           placeholder="cerca prodotto"
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
            <th width="10%">Codice</th>
            <th width="10%">Tipo</th>
            <th>Nome</th>
            <th width="10%" class="text-right">Kg.</th>
            <th width="10%" class="text-right">Q.tà</th>
            <th width="10%" class="text-right">Punti</th>
            <th width="120px"></th>
        </tr>
        </thead>

        <tbody>

        @foreach($products as $product)

            <tr>
                <td class="align-middle">{{ $product->cod }}</td>
                <td class="align-middle">
                    @if($product->type == 'fead')
                    {{ $product->type }}
                    @endif
                </td>
                <td class="align-middle">{{ $product->name }}</td>
                <td class="align-middle text-right">{{ $product->kg_total }}</td>
                <td class="align-middle text-right">{{ $product->amount_total }}</td>
                <td class="align-middle text-right">{{ $product->points }}</td>
                <td class="text-center">

                    <div class="row no-gutters">
                        <div class="col">
                            <a href="{{ route('products.edit', $product->id) }}"
                               class="btn btn-success">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                        <div class="col">

                            <button type="button"
                                    class="btn btn-danger"
                                    data-toggle="modal"
                                    data-target="#deleteModal"
                                    data-href="{{ route('products.destroy', $product->id) }}">
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

    {{ $products->links() }}

@endsection
