@extends('layouts.card')

@section('card-body')

    <style>

        #customer-data,
        #product-container,
        #product-data,
        #order-summary {
            display: none;
        }

    </style>

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
        }

        window.addEventListener('load', function () {

            $('#customer_cod').focus();

            // Customer Search
            $(document).on('input', '#customer_cod', function () {

                var ObjCustomerData = $('#customer-data');
                var ObjProductContainer = $('#product-container');

                dataSearch(ObjCustomerData, '{{ route('shop.search') }}/?', $(this).val(), 'customer_cod', function () {

                    ObjCustomerData.css('display', 'none');
                    ObjProductContainer.css('display', 'none');

                }, function () {

                    summarySet();

                    $('#customer-data-search').css('display', 'none');
                    $('#order-summary').css('display', 'block');
                    ObjCustomerData.css('display', 'block');
                    ObjProductContainer.css('display', 'block');
                    $('#product_cod').focus();

                });

            });

            // Product Search
            $(document).on('input', '#product_cod', function () {

                var ObjProductData = $('#product-data');
                var ObjProductDataTR = ObjProductData.find('tbody > tr').last();
                var ObjProductDataTRClone = ObjProductDataTR.clone().css('display', 'none');

                dataSearch(ObjProductDataTR, '{{ route('shop.search') }}/?', $(this).val(), 'product_cod', function () {

                    ObjProductDataTR.css('display', 'table-row');
                    ObjProductDataTR.after(ObjProductDataTRClone);

                }, function () {

                    summarySet();

                    ObjProductData.css('display', 'block');
                    $('#product_cod').val('').focus();

                });

            });

            $(document).on('click', '.del-item', function () {

                $(this).closest('tr').remove();

                summarySet();

                $('#product_cod').val('').focus();

            });

        });

    </script>

    <form action="#" method="post">

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

                    <div class="h4" style="padding: 6px; margin-bottom: 15px;">
                        Cod. Cliente <span data-id="cod"></span>
                    </div>

                    <table class="table table-borderless table-success">
                        <thead>
                        <tr>
                            {{--<th>Codice</th>--}}
                            <th></th>
                            {{--<th>Cognome</th>--}}
                            {{--<th class="">Indirizzo</th>--}}
                            <th class="text-right">Punti</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            {{--<td class="align-middle"
                                data-id="cod"></td>--}}
                            <td class="align-middle">
                                <span data-id="name"></span>
                                <span data-id="surname"></span>
                                <br>
                                <small>
                                    <span data-id="address"></span>
                                </small>
                            </td>
                            {{--<td class="align-middle"
                                data-id="surname"></td>--}}
                            {{--<td class="align-middle"
                                data-id="address"></td>--}}
                            <td class="align-middle text-right h1"
                                data-id="points"></td>
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
