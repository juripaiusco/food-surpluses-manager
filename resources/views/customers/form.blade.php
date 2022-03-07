@extends('layouts.card')

@section('card-body')

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
            <input type="submit" class="btn btn-success" value="@if(isset($customer->id)) Modifica @else Inserisci @endif">

        </div>

    </form>

    @if(count($customer->order) > 0)

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
