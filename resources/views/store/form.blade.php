@extends('layouts.card')

@section('card-body')

    @include('js/search')

    <script language="JavaScript">

        window.addEventListener('load', function () {

            $('#product_cod').focus();

            $(document).on('input', '#product_cod', function () {

                var ObjProductContainer = $('#product-container');

                dataSearch(ObjProductContainer, '{{ route('store.search') }}/?', $(this).val(), 'product_cod', function () {

                }, function () {

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
