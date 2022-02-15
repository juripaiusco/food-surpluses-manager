@extends('layouts.card')

@section('card-body')

    <style>

        #customer-data,
        #product-container,
        #product-data {
            display: none;
        }

    </style>
    <script language="JavaScript">

        function dataSet(ObjDataContainer, jsonData)
        {
            $.each(jsonData, function(k, v) {

                ObjDataContainer.find('[data-id="' + k + '"]').html(v);

            });
        }

        function dataSearch(ObjData, codSearch, urlQuery, callforward, callback)
        {
            if (callforward != null)
                callforward();

            $.getJSON('{{ route('shop.search') }}/?' + urlQuery + '=' + codSearch, function (d) {

                if (d != null) {

                    dataSet(ObjData, d);

                    if (callback != null)
                        callback();

                }

            });
        }

        window.addEventListener('load', function () {

            $('#customer_cod').focus();

            // Customer Search
            $(document).on('input', '#customer_cod', function () {

                var ObjCustomerData = $('#customer-data');
                var ObjProductContainer = $('#product-container');

                dataSearch(ObjCustomerData, $(this).val(), 'customer_cod', function () {

                    ObjCustomerData.css('display', 'none');
                    ObjProductContainer.css('display', 'none');

                }, function () {

                    ObjCustomerData.css('display', 'block');
                    ObjProductContainer.css('display', 'block');
                    $('#product_cod').focus();

                });

            });

            // Product Search
            $(document).on('input', '#product_cod', function () {

                dataSearch($('#product-data'), $(this).val(), 'product_cod', null, function () {

                    $('#product-data').css('display', 'block');
                    $('#product_cod').focus();

                });

            });

        });

    </script>

    <form action="#" method="post">

        @csrf

        <div class="form-group">
            <input type="text"
                   class="form-control"
                   id="customer_cod"
                   name="customer_cod"
                   placeholder="Inserisci il codice cliente">
        </div>

        <div id="customer-data">

            <table class="table table-success">
                <thead>
                <tr>
                    <th>Codice</th>
                    <th>Nome</th>
                    <th>Cognome</th>
                    <th class="">Indirizzo</th>
                    <th class="text-right">Punti</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td class="align-middle"
                        data-id="cod"></td>
                    <td class="align-middle"
                        data-id="name"></td>
                    <td class="align-middle"
                        data-id="surname"></td>
                    <td class="align-middle"
                        data-id="address"></td>
                    <td class="align-middle text-right h1"
                        data-id="points"></td>
                </tr>
                </tbody>
            </table>

            <br>
            <hr width="50%">
            <br>

        </div>

        <div id="product-container">

            <div class="form-group">
                <input type="text"
                       class="form-control"
                       id="product_cod"
                       name="product_cod"
                       placeholder="Inserisci il codice prodotto">
            </div>

            <div id="product-data">

                <table class="table table-success">
                    <thead>
                    <tr>
                        <th>Codice</th>
                        <th>Tipo</th>
                        <th>Nome</th>
                        <th class="text-right">Punti</th>
                        <th class="text-right">Kg.</th>
                        <th class="text-right">Q.tà</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td class="align-middle"
                            data-id="cod"></td>
                        <td class="align-middle"
                            data-id="type"></td>
                        <td class="align-middle"
                            data-id="name"></td>
                        <td class="align-middle text-right"
                            data-id="points"></td>
                        <td class="align-middle text-right"
                            data-id="kg"></td>
                        <td class="align-middle text-right"
                            data-id="amount"></td>
                    </tr>
                    </tbody>
                </table>

                <br>
                <hr width="50%">
                <br>

            </div>

        </div>

        {{--<ul class="nav nav-pills justify-content-center" id="pills-tab" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active"
                   id="pills-home-tab"
                   data-toggle="pill"
                   href="#pills-home"
                   role="tab"
                   aria-controls="pills-home"
                   aria-selected="true">1</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link"
                   id="pills-profile-tab"
                   data-toggle="pill"
                   href="#pills-profile"
                   role="tab"
                   aria-controls="pills-profile"
                   aria-selected="false">2</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link"
                   id="pills-contact-tab"
                   data-toggle="pill"
                   href="#pills-contact"
                   role="tab"
                   aria-controls="pills-contact"
                   aria-selected="false">3</a>
            </li>
        </ul>
        <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">

            </div>
            <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">

            </div>
            <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">

            </div>
        </div>--}}

    </form>

@endsection
