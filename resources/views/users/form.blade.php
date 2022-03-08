@extends('layouts.card')

@section('card-body')

    <form action="{{ isset($user->id) ? route('users.update', $user->id) : route('users.store') }}" method="post">

        @csrf

        <div class="row">
            <div class="col">

                <div class="form-group">
                    <label for="cod">Nome</label>
                    <input type="text"
                           class="form-control"
                           id="name"
                           name="name"
                           placeholder="Nome"
                           @if(isset($user->id))
                           value="{{ $user->name }}"
                        @endif>
                </div>

            </div>
            <div class="col">

                <div class="form-group">
                    <label for="cod">Email</label>
                    <input type="text"
                           class="form-control"
                           id="email"
                           name="email"
                           placeholder="email"
                           @if(isset($user->id))
                           value="{{ $user->email }}"
                        @endif>
                </div>

            </div>
        </div>

        @if(!isset($user->id))
            <div class="form-group">
                <label for="cod">Password</label>
                <input type="password"
                       class="form-control"
                       id="password"
                       name="password"
                       placeholder="password"
                       @if(isset($user->id))
                       value="{{ $user->email }}"
                    @endif>
            </div>
        @endif

        <div class="text-right">

            <button type="submit" class="btn btn-success">Salve</button>

        </div>

    </form>

@endsection
