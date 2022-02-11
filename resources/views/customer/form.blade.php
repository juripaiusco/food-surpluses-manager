@extends('layouts.card')

@section('content')

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
                    <label for="name">Cognome</label>
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
            <label for="name">Indirizzo</label>
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
            <div class="col-6">

                <div class="form-group">
                    <label for="name">Codice</label>
                    <input type="text"
                           class="form-control disabled"
                           id="cod"
                           placeholder="cod"
                           readonly disabled
                           @if(isset($customer->id))
                           value="{{ $customer->cod }}"
                        @endif>
                </div>

            </div>
        </div>

        <input type="submit" class="btn btn-success" value="Modifica">

    </form>

@endsection
