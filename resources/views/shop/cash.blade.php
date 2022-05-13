@extends('layouts.card')

@section('card-body')

    @php
        $route_name = current(explode('.', \Illuminate\Support\Facades\Route::currentRouteName()));
    @endphp

    <style>

        #customer-data,
        #product-container,
        #product-data,
        #order-summary {
            display: none;
        }

        @if(!Auth::user()->json_retails)
        form {
            display: none;
        }
        @endif

    </style>

    @if(!Auth::user()->json_retails)

        <div class="text-center alert alert-warning">
            <h1>L'utente non gestisce alcun negozio.</h1>
            <span class="h4">
                Collegare negozio all'utente per poter effettuare l'ordine.
            </span>
        </div>

    @endif

    @include('js/search')

    <script language="JavaScript">

        function summarySet()
        {
            var Obj = $('#order-summary');

            // - - -

            var customerPoints = parseInt($('#customer-data').find('[data-id="points"]').html());

            Obj.find('[data-id="customer-points"]').html(customerPoints);

            // - - -

            var productPointsSUM = 0;

            $('#product-data').find('[data-id="points"]').each(function () {

                productPointsSUM += parseInt($(this).html()) || 0;

            });

            Obj.find('[data-id="product-points-sum"]').html(productPointsSUM);

            // - - -

            var pointsResult = customerPoints - productPointsSUM;

            Obj.find('[data-id="points-result"]').html(pointsResult);

            if (productPointsSUM === 0 || pointsResult < 0) {

                $('#shopSubmit').attr('disabled', true);

            } else {

                $('#shopSubmit').attr('disabled', false);
            }
        }

        window.addEventListener('load', function () {

            $(document).on("keydown", ":input:not(textarea):not(:submit)", function(event) {

                if (event.keyCode == 13) {
                    return false;
                }

            });

            $('#customer_cod').focus();

            // Customer Search
            $(document).on('input', '#customer_cod', function () {

                if ($(this).val().length === 8) {

                    var ObjCustomerData = $('#customer-data');
                    var ObjProductContainer = $('#product-container');

                    dataSearch(ObjCustomerData, '{{ route('shop.search') }}/?', $(this).val(), 'customer_cod', function () {

                        ObjCustomerData.css('display', 'none');
                        ObjProductContainer.css('display', 'none');

                    }, function (d) {

                        if (d == null) {

                            alert('Cliente non trovato');

                        } else {

                            $('#customer-data-search').css('display', 'none');
                            $('#order-summary').css('display', 'block');
                            ObjCustomerData.css('display', 'block');
                            ObjProductContainer.css('display', 'block');
                            $('#product_cod').focus();

                            summarySet();

                        }

                    });

                }

            });

            // Product Search
            $(document).on('input', '#product_cod', function () {

                if ($(this).val().length === 7) {

                    var ObjProductData = $('#product-data');
                    var ObjProductDataTR = ObjProductData.find('tbody > tr').first();
                    var ObjProductDataTRClone = ObjProductDataTR.clone()
                        .css('opacity', 0)
                        .css('display', 'none');
                    var CustomerCod = $('#customer_cod').val();

                    dataSearch(
                        ObjProductDataTR,
                        '{{ route('shop.search') }}/?customer_cod=' + CustomerCod + '&',
                        $(this).val(),
                        'product_cod',

                        function () {

                            ObjProductDataTR.before(ObjProductDataTRClone);

                        }, function (d) {

                            if (d == null) {

                                ObjProductDataTRClone.remove();
                                $('#product_cod').val('').focus();
                                alert('Prodotto non trovato');

                            } else {

                                if (d.bought === 1) {

                                    ObjProductDataTR.find('.bought').html(
                                        '<div class="spinner-grow spinner-grow-sm text-danger" role="status">'+
                                        '<span class="sr-only">Loading...</span>'+
                                        '</div>'
                                    );

                                }

                                if (d.amount_total <= 0) {

                                    ObjProductDataTRClone.remove();
                                    alert('Prodotto esaurito');

                                } else {

                                    ObjProductDataTR.css('display', 'table-row');
                                    ObjProductDataTR.animate({
                                        opacity: '1'
                                    }, 800);

                                    ObjProductData.css('display', 'block');
                                    $('#product_cod').val('').focus();

                                    summarySet();
                                }
                            }
                        }
                    );

                }

            });

            $(document).on('click', '.del-item', function () {

                $(this).closest('tr').remove();

                $('#product_cod').val('').focus();

                summarySet();

            });

        });

    </script>

    <form action="{{ route('shop.store') }}" method="post">

        @csrf

        <div id="customer-data-search" class="form-group">
            <input type="text"
                   class="form-control"
                   id="customer_cod"
                   name="customer_cod"
                   placeholder="Inserisci il codice cliente">
        </div>

        <div class="row">
            <div class="col-lg-8">

                <div id="product-container">

                    <div class="form-group">
                        <input type="text"
                               class="form-control"
                               id="product_cod"
                               name="product_cod"
                               placeholder="Inserisci il codice prodotto">
                    </div>

                    <div id="product-data">

                        <table class="table table-striped table-borderless table-hover table-success">
                            <thead>
                            <tr>
                                <th width="1%"></th>
                                <th width="1%"></th>
                                <th>Codice</th>
                                <th>Tipo</th>
                                <th>Nome</th>
                                <th class="text-right">Kg.</th>
                                <th class="text-right">Q.tà</th>
                                <th class="text-right">Punti</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td class="align-middle">

                                    <button type="button"
                                            class="btn btn-danger btn-sm del-item">
                                        <i class="far fa-trash-alt"></i>
                                    </button>

                                    <input type="hidden"
                                           name="product_id[]"
                                           data-id="id">

                                </td>
                                <td class="align-middle">
                                    <div class="bought"></div>
                                </td>
                                <td class="align-middle"
                                    data-id="cod"></td>
                                <td class="align-middle"
                                    data-id="type"></td>
                                <td class="align-middle"
                                    data-id="name"></td>
                                <td class="align-middle text-right"
                                    data-id="kg"></td>
                                <td class="align-middle text-right"
                                    data-id="amount"></td>
                                <td class="align-middle text-right h4"
                                    data-id="points"></td>
                            </tr>
                            </tbody>
                        </table>

                    </div>

                </div>

            </div>
            <div class="col-lg-4">

                <div id="customer-data">

                    <input type="hidden"
                           name="customer_id"
                           data-id="id">

                    <div class="h4" style="padding: 6px; margin-bottom: 15px;">
                        Cod. Cliente <span data-id="cod"></span>
                    </div>

                    <table class="table table-borderless table-success">
                        {{--<thead>
                        <tr>
                            --}}{{--<th>Codice</th>--}}{{--
                            <th></th>
                            --}}{{--<th>Cognome</th>--}}{{--
                            --}}{{--<th class="">Indirizzo</th>--}}{{--
                            <th class="text-right">Punti</th>
                        </tr>
                        </thead>--}}
                        <tbody>
                        <tr>
                            {{--<td class="align-middle"
                                data-id="cod"></td>--}}
                            <td class="align-top">

                                <span data-id="name" class="h5"></span>
                                <span data-id="surname" class="h5"></span>

                                <br>

                                Componenti
                                <strong>
                                    <span data-id="family_number"></span>
                                </strong>

                            </td>
                            {{--<td class="align-middle"
                                data-id="surname"></td>--}}
                            {{--<td class="align-middle"
                                data-id="address"></td>--}}
                            <td class="align-top text-right">
                                <strong>Punti</strong>
                                <h1 data-id="points"></h1>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                    <br>

                </div>

                <div id="order-summary">

                    <table class="table table-sm">
                        <tbody>
                        <tr>
                            <td class="align-middle">Punti cliente</td>
                            <td class="align-middle text-right"
                                data-id="customer-points"></td>
                        </tr>
                        <tr>
                            <td class="align-middle">Punti prodotto</td>
                            <td class="align-middle text-right"
                                data-id="product-points-sum"></td>
                        </tr>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td class="align-middle">Punti rimanenti</td>
                            <td class="align-middle text-right h3"
                                data-id="points-result"></td>
                        </tr>
                        </tfoot>
                    </table>

                    <button type="submit"
                            id="shopSubmit"
                            class="btn btn-block btn-lg btn-success" disabled>Termina Ordine</button>

                </div>

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
