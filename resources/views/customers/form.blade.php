@extends('layouts.card')

@section('card-body')

    @php
        $route_name = current(explode('.', \Illuminate\Support\Facades\Route::currentRouteName()));
    @endphp

    <form action="{{ isset($customer->id) ? route('customers.update', $customer->id) : route('customers.store') }}" method="post">

        @csrf

        <h2>Scheda cliente</h2>

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

        <div class="row">
            <div class="col-8">

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

            </div>
            <div class="col">

                <div class="form-group">
                    <label for="cod">Componenti famiglia</label>
                    <input type="text"
                           class="form-control"
                           id="family_number"
                           name="family_number"
                           placeholder="Numero di componenti presenti in famiglia"
                           @if(isset($customer->id))
                           value="{{ $customer->family_number }}"
                        @endif>
                </div>

            </div>
        </div>

        <div class="row">
            <div class="col">

                <div class="form-group">
                    <label for="cod">n. Assistito</label>
                    <input type="text"
                           class="form-control"
                           id="number"
                           name="number"
                           placeholder="n. assistito"
                           @if(isset($customer->id))
                           value="{{ $customer->number }}"
                        @endif>
                </div>

            </div>
            <div class="col">

                <div class="form-group">
                    <label for="cod">n. Tessera</label>
                    <input type="text"
                           class="form-control border-dark"
                           id="cod"
                           name="cod"
                           placeholder="Codice"
                           @if(isset($customer->id))
                           value="{{ $customer->cod }}"
                        @endif>
                </div>

            </div>
            <div class="col">

                <div class="form-group">
                    <label for="points">Punti da rinnovare a fine mese</label>
                    <input type="text"
                           class="form-control border-success"
                           id="points_renew"
                           name="points_renew"
                           @if(isset($customer->id))
                           value="{{ $customer->points_renew }}"
                        @endif>
                </div>

            </div>
            <div class="col">

                <div class="form-group">
                    <label for="points">Punti rimanenti per questo mese</label>
                    <input type="text"
                           class="form-control border-info"
                           id="points"
                           name="points"
                           @if(isset($customer->id))
                           value="{{ $customer->points }}"
                        @endif>
                </div>

            </div>
        </div>

        <div class="text-right">

            <a href="javascript: history.go(-1);" class="btn btn-secondary">Annulla</a>
            <input type="submit" class="btn btn-success" value="@if(isset($customer->id)) Modifica @else Inserisci @endif">

        </div>

    </form>

    @if(isset($customer) && count($customer->order) > 0)

        <br>
        <h2>Ordini eseguiti (ultimi 10)</h2>

        <table class="table table-info table-striped table-hover">
            <thead>
            <tr>
                <th class="w-25">Data ordine</th>
                <th class="w-25">Riferimento</th>
                <th class="text-right">Punti</th>
            </tr>
            </thead>

            <tbody>
            @foreach($customer->order as $order)

                <tr>
                    <td>{{ $order->date }}</td>
                    <td>{{ $order->reference }}</td>
                    <td class="text-right">{{ $order->points }}</td>
                </tr>

            @endforeach
            </tbody>
        </table>

    @endif

@endsection
