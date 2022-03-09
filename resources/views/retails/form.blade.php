@extends('layouts.card')

@section('card-body')

    <form action="{{ isset($retail->id) ? route('retails.update', $retail->id) : route('retails.store') }}" method="post">

        @csrf

        <div class="row">
            <div class="col-12">

                <div class="form-group">
                    <label for="cod">Nome</label>
                    <input type="text"
                           class="form-control"
                           id="name"
                           name="name"
                           placeholder="Nome"
                           @if(isset($retail->id))
                           value="{{ $retail->name }}"
                        @endif>
                </div>

            </div>
            <div class="col-2">

                <div class="form-group">
                    <label for="cod">CAP</label>
                    <input type="text"
                           class="form-control"
                           id="zip"
                           name="zip"
                           placeholder="CAP"
                           @if(isset($retail->id))
                           value="{{ $retail->zip }}"
                        @endif>
                </div>

            </div>
            <div class="col">

                <div class="form-group">
                    <label for="cod">Indirizzo</label>
                    <input type="text"
                           class="form-control"
                           id="address"
                           name="address"
                           placeholder="Indirizzo"
                           @if(isset($retail->id))
                           value="{{ $retail->address }}"
                        @endif>
                </div>

            </div>
            <div class="col-4">

                <div class="form-group">
                    <label for="cod">Città</label>
                    <input type="text"
                           class="form-control"
                           id="city"
                           name="city"
                           placeholder="Città"
                           @if(isset($retail->id))
                           value="{{ $retail->city }}"
                        @endif>
                </div>

            </div>
        </div>

        <div class="text-right">

            <a href="javascript: history.go(-1);" class="btn btn-secondary">Annulla</a>
            <button type="submit" class="btn btn-success">@if(isset($retail->id)) Modifica @else Inserisci @endif</button>

        </div>

    </form>

@endsection
