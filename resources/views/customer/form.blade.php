@extends('layouts.card')

@section('card-body')

    <form action="{{ isset($customer->id) ? route('customer.update', $customer->id) : route('customer.store') }}" method="post">

        @csrf

        <div class="row">
            <div class="col">

                <div class="form-group">
                    <label for="name">Nome</label>
                    <input type="text"
                           class="form-control"
                           id="name"
                           name="name"
                           placeholder="Nome"
                           @if(isset($customer->id))
                           value="{{ $customer->name }}"
                        @endif>
                </div>

            </div>
            <div class="col">

                <div class="form-group">
                    <label for="surname">Cognome</label>
                    <input type="text"
                           class="form-control"
                           id="surname"
                           name="surname"
                           placeholder="Cognome"
                           @if(isset($customer->id))
                           value="{{ $customer->surname }}"
                        @endif>
                </div>

            </div>
        </div>

        <div class="form-group">
            <label for="address">Indirizzo</label>
            <input type="text"
                   class="form-control"
                   id="address"
                   name="address"
                   placeholder="Indirizzo"
                   @if(isset($customer->id))
                   value="{{ $customer->address }}"
                @endif>
        </div>

        <div class="row">
            <div class="col">

                <div class="form-group">
                    <label for="cod">Codice</label>
                    <input type="text"
                           class="form-control disabled"
                           id="cod"
                           placeholder="Codice"
                           readonly disabled
                           @if(isset($customer->id))
                           value="{{ $customer->cod }}"
                        @endif>
                </div>

            </div>

            <div class="col">

                <div class="form-group">
                    <label for="points">Punti</label>
                    <input type="text"
                           class="form-control disabled"
                           id="points"
                           readonly disabled
                           @if(isset($customer->id))
                           value="{{ $customer->points }}"
                        @endif>
                </div>

            </div>
        </div>

        <div class="text-right">

            <a href="javascript: history.go(-1);" class="btn btn-secondary">Annulla</a>
            <input type="submit" class="btn btn-success" value="Modifica">

        </div>

    </form>

@endsection
