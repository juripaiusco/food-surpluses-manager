@extends('layouts.card')

@section('card-body')

    <style>

        #product-container {
            display: none;
        }

    </style>
    <script language="JavaScript">

        window.addEventListener('load', function () {

            $('#customer_cod').focus();

            // Customer Search
            $(document).on('input', '#customer_cod', function () {

                $('#product-container').css('display', 'none');

                var Obj = $(this);
                var ObjVal = Obj.val();

                $('#customer-data').load('{{ route('shop.customerSearch') }}/?customer_cod=' + ObjVal, function () {

                    // $('#product-container').css('display', 'block');

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

        <div id="customer-data"></div>

        <div id="product-container">

            <div class="form-group">
                <input type="text"
                       class="form-control"
                       id="product_cod"
                       name="product_cod"
                       placeholder="Inserisci il codice prodotto">
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
