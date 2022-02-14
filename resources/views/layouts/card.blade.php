@extends('layouts.app')

@section('content')

    <div class="container">

        <div class="card">
            <div class="card-header">

                {{ __('layout.' . current(explode('.', Route::currentRouteName())) . '.title') }}

            </div>
            <div class="card-body">

                @yield('card-body')

            </div>
        </div>

        @yield('paginate')

    </div>

@endsection
