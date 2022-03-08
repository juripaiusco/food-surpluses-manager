@extends('layouts.card')

@section('card-body')

    <script language="JavaScript">

        window.onload = function() {

            $('#deleteModal').on('show.bs.modal', function(e) {
                $(this).find('#btn-del').attr('href', $(e.relatedTarget).data('href'));
            });

        };

    </script>

    <div class="row">
        <div class="col">

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

    <table class="table table-striped table-hover">

        <thead>
        <tr>
            <th width="10%" class="text-center">Data</th>
            <th width="10%" class="text-center">Riferimento</th>
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

    <!-- Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Elimina</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Confermi l'eminazione?

                    <br /><br />

                    <div class="row">
                        <div class="col-lg-6">

                            <a href="#" id="btn-del" class="btn btn-danger btn-block">Sì</a>

                        </div>
                        <div class="col-lg-6">

                            <button type="button" class="btn btn-secondary btn-block" data-dismiss="modal">No</button>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection

@section('paginate')

    <br>

    {{ $orders->links() }}

@endsection
