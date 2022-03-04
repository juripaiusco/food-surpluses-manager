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
                var ObjProductContainer = $('#product-container');

                dataSearch(ObjProductContainer, '{{ route('store.search') }}/?', ObjVal, 'product_cod', function () {

                    $('#product-not-find').css('display', 'none');
                    $('#product-container').css('display', 'none');

                }, function (d) {

                    if (d == null) {

                        $('#product-not-find').css('display', 'block');
                        $('#product-not-find').find('a').attr('href', '{{ route('products.create') }}/?cod=' + ObjVal);

                    } else {

                        $('#product-container').css('display', 'block');

                    }

                });

            });

        });

    </script>

    <div id="customer-data-search" class="form-group">
        <input type="text"
               class="form-control text-center"
               id="product_cod"
               name="product_cod"
               placeholder="Inserisci codice prodotto">
    </div>

    <div id="product-not-find">

        <br>

        <div class="alert alert-primary text-center" role="alert">
            Prodotto non trovato.
        </div>

        <a class="btn btn-lg btn-block btn-primary"
           href="{{ route('products.create') }}">
            Inserisci nuovo prodotto
        </a>

    </div>

    <div id="product-container">

        <table class="table">
            <thead>
            <tr>
                <th width="25%" class="text-center">Codice</th>
                <th width="25%" class="text-center">Data di carico</th>
                <th width="25%" class="text-center">Kg.</th>
                <th width="25%" class="text-center">Q.tà</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="align-middle text-center"
                    data-id="cod"></td>
                <td>

                    <input type="text"
                           class="form-control text-center"
                           id="date_input"
                           name="date_input"
                           value="{{ date('d/m/Y') }}"
                           placeholder="{{ date('d/m/Y') }}">

                </td>
                <td>

                    <input type="text"
                           class="form-control text-center"
                           id="kg"
                           name="kg"
                           placeholder="kg. prodotto">

                </td>
                <td>

                    <input type="text"
                           class="form-control text-center"
                           id="amount"
                           name="amount"
                           placeholder="q.tà prodotto">

                </td>
            </tr>
            </tbody>
        </table>

    </div>

@endsection
