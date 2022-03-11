@extends('layouts.card')

@section('card-body')

    @include('js/search')

    <style>

        #product-not-find,
        #product-container {
            display: none;
        }

    </style>

    <script language="JavaScript">

        window.addEventListener('load', function () {

            $('#product_cod').focus();

            $(document).on('input', '#product_cod', function () {

                var ObjVal = $(this).val();
                var ObjProductNotFind = $('#product-not-find');
                var ObjProductContainer = $('#product-container');

                dataSearch(ObjProductContainer, '{{ route('store.search') }}/?', ObjVal, 'product_cod', function () {

                    ObjProductNotFind.css('display', 'none');
                    ObjProductContainer.css('display', 'none');

                }, function (d) {

                    if (d == null) {

                        ObjProductNotFind.css('display', 'block');
                        ObjProductNotFind.find('a').attr('href', '{{ route('products.create') }}/?cod=' + ObjVal);

                    } else {

                        ObjProductContainer.css('display', 'block');

                        switch (d.type) {
                            case 'fead':

                                ObjProductContainer.find('#kg')
                                    .attr('readonly', false)
                                    .attr('disabled', false)
                                    .focus();

                                break;

                            case 'fead no':

                                ObjProductContainer.find('#kg')
                                    .attr('readonly', true)
                                    .attr('disabled', true);

                                ObjProductContainer.find('#amount').focus();

                                break;
                        }

                    }

                });

            });

        });

    </script>

    <div id="customer-data-search" class="form-group">
        <input type="text"
               class="form-control form-control-lg text-center"
               id="product_cod"
               name="product_cod"
               placeholder="Inserisci codice prodotto">
    </div>

    <div id="product-not-find">

        <br>

        <div class="alert alert-warning text-center" role="alert">
            Prodotto non trovato.
        </div>

        <a class="btn btn-lg btn-block btn-primary"
           href="{{ route('products.create') }}">
            Inserisci nuovo prodotto
        </a>

    </div>

    <div id="product-container">

        <form action="{{ route('store.store') }}" method="post" autocomplete="off">

            @csrf

            <br>
            <div class="h1 text-center">
                <span data-id="cod"></span>
                <br>
                <span data-id="name"></span>
            </div>

            <br>

            <div class="text-center h3">
                Inserisci qui sotto le quantità del prodotto ricercato
            </div>

            <br>

            <div class="row alert alert-info">
                <div class="col">

                    <div class="form-group text-center">
                        <label for="date">Data</label>
                        <input type="text"
                               class="form-control text-center"
                               id="date"
                               name="date"
                               value="{{ date('d/m/Y H:i:s') }}"
                               placeholder="{{ date('d/m/Y H:i:s') }}">
                    </div>

                </div>
                <div class="col">

                    <div class="form-group text-center">
                        <label for="kg">Kg totali da inserire</label>
                        <input type="text"
                               class="form-control text-center"
                               id="kg"
                               name="kg"
                               placeholder="kg. prodotto"
                               autocomplete="off"
                               autofill="off">
                    </div>

                </div>
                <div class="col">

                    <div class="form-group text-center">
                        <label for="amount">Q.tà totale da inserire</label>
                        <input type="text"
                               class="form-control text-center"
                               id="amount"
                               name="amount"
                               placeholder="q.tà prodotto"
                               autocomplete="off"
                               autofill="off">
                    </div>

                </div>
            </div>

            <br>

            <div class="row">
                <div class="col-8"></div>
                <div class="col">
                    <button class="btn btn-success btn-lg btn-block">
                        Salva
                    </button>

                    <br>

                    <div class="text-center">
                        Oppure premi INVIO
                    </div>
                </div>
            </div>

            {{--<table class="table">
                <thead>
                <tr>
                    <th class="text-center w-25">Codice</th>
                    <th class="text-center w-25">Data di carico</th>
                    <th class="text-center w-25">Kg.</th>
                    <th class="text-center w-25">Q.tà</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td class="align-middle text-center"
                        data-id="cod"></td>
                    <td>

                        <input type="text"
                               class="form-control text-center"
                               id="date"
                               name="date"
                               value="{{ date('d/m/Y H:i:s') }}"
                               placeholder="{{ date('d/m/Y H:i:s') }}">

                    </td>
                    <td>

                        <input type="text"
                               class="form-control text-center"
                               id="kg"
                               name="kg"
                               placeholder="kg. prodotto"
                               autocomplete="off"
                               autofill="off">

                    </td>
                    <td>

                        <input type="text"
                               class="form-control text-center"
                               id="amount"
                               name="amount"
                               placeholder="q.tà prodotto"
                               autocomplete="off"
                               autofill="off">

                    </td>
                </tr>
                <tr>
                    <td colspan="3"></td>
                    <td>

                        <button class="btn btn-block btn-success">Salva</button>

                        <br>

                        <div class="text-center">
                            Oppure premi INVIO
                        </div>

                    </td>
                </tr>
                </tbody>
            </table>--}}

            <input type="hidden" name="id" data-id="id">
            <input type="hidden" name="cod" data-id="cod">

        </form>

    </div>

@endsection
