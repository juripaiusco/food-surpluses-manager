@extends('layouts.card')

@section('card-body')

    <script language="JavaScript">

        function tipologiaOnChange(type) {

            $typeElementValue = $('#type').find('option:selected').val().toLowerCase();

            /*if (type) {
                $typeElementValue = type;
            }*/

            switch ($typeElementValue) {
                case 'fead':

                    $('#kg')
                        .attr('disabled', false)
                        .attr('readonly', false);

                    $('#amount')
                        .attr('disabled', false)
                        .attr('readonly', false);

                    break;

                case 'fead no':

                    $('#kg')
                        .attr('disabled', true)
                        .attr('readonly', true);

                    $('#amount')
                        .attr('disabled', false)
                        .attr('readonly', false);

                    break;
            }

            return false;

        }

        window.addEventListener('load', function () {

            @if(isset($product->id))
                tipologiaOnChange('{{ $product->type }}');
            @endif

        });

    </script>

    <form action="{{ isset($product->id) ? route('products.update', $product->id) : route('products.store') }}" method="post">

        @csrf

        <div class="row">
            <div class="col">

                <div class="form-group">
                    <label for="name">Tipo</label>
                    <select class="form-control"
                            id="type"
                            name="type"
                            onchange="tipologiaOnChange('')">

                        <option value="">Seleziona Tipologia</option>

                        @foreach($type_array as $type)
                            <option value="{{ $type }}"
                                    @if(isset($product->type) && $product->type == $type)
                                    selected
                                @endif>{{ $type }}</option>
                        @endforeach

                    </select>
                </div>

            </div>
            <div class="col">

                <div class="form-group">
                    <label for="cod">Codice</label>
                    <input type="text"
                           class="form-control disabled"
                           id="cod"
                           name="cod"
                           placeholder="Codice"
                           @if(isset($product->id))
                           value="{{ $product->cod }}"
                        @endif>
                </div>

            </div>
        </div>

        <div class="row">
            <div class="col-6">

                <div class="form-group">
                    <label for="name">Nome</label>
                    <input type="text"
                           class="form-control"
                           id="name"
                           name="name"
                           placeholder="Nome"
                           @if(isset($product->id))
                           value="{{ $product->name }}"
                        @endif>
                </div>

            </div>
            <div class="col">

                <div class="form-group">
                    <label for="name">Punti</label>
                    <input type="text"
                           class="form-control"
                           id="points"
                           name="points"
                           placeholder="Punti prodotto"
                           @if(isset($product->id))
                           value="{{ $product->points }}"
                        @endif>
                </div>

            </div>
            <div class="col">

                <div class="form-group">
                    <label for="kg">&nbsp;</label>

                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">Kg.</div>
                        </div>

                        <input type="text"
                               class="form-control text-center"
                               id="kg"
                               name="kg"
                               placeholder="es. 0.5"
                               @if(isset($product->id))
                               value="{{ $product->kg }}"
                            @endif
                               @if(!isset($product->id))
                               disabled readonly
                            @endif>

                    </div>

                </div>

            </div>

            <div class="col">

                <div class="form-group">
                    <label for="amount">&nbsp;</label>

                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">Q.tà</div>
                        </div>

                        <input type="text"
                               class="form-control text-center"
                               id="amount"
                               name="amount"
                               placeholder="es. 2"
                               @if(isset($product->id))
                               value="{{ $product->amount }}"
                            @endif
                               @if(!isset($product->id))
                               disabled readonly
                            @endif>

                    </div>

                </div>

            </div>
        </div>

        <div class="text-right">

            <a href="javascript: history.go(-1);" class="btn btn-secondary">Annulla</a>
            <input type="submit" class="btn btn-success" value="@if(isset($product->id)) Modifica @else Inserisci @endif">

        </div>

    </form>

@endsection
