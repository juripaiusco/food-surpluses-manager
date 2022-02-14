@extends('layouts.app')

@section('card')

    <div class="container">

        <div class="card">
            <div class="card-header">

                {{ __('layout.' . current(explode('.', Route::currentRouteName())) . '.title') }}

            </div>
            <div class="card-body">

                @yield('content')

            </div>
        </div>

        @yield('paginate')

    </div>

@endsection
