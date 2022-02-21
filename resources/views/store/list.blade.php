@extends('layouts.card')

@section('card-body')

    <a href="{{ route('store.create') }}" class="btn btn-primary btn-block btn-lg">Inserisci prodotto</a>

@endsection

@section('paginate')

    <br>

    {{--{{ $store->links() }}--}}

@endsection
