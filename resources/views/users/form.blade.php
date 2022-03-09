@extends('layouts.card')

@section('card-body')

    <form action="{{ isset($user->id) ? route('users.update', $user->id) : route('users.store') }}" method="post">

        @csrf

        <h2>Dati utente</h2>

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

        <br>

        <h2>Moduli attivi</h2>

        <div class="row">

            @foreach($modules_array as $k => $module)

                @if(isset($module['title']))

                    <div class="col-3">

                        <div class="custom-control custom-switch custom-switch-md form-control-lg">
                            <input type="checkbox"
                                   class="custom-control-input"
                                   id="{{ $k }}"
                                   name="modules[{{ $k }}]"
                            @if(isset($modules[$k]) && $modules[$k] == 'on')
                                checked
                                @endif>
                            <label class="custom-control-label" for="{{ $k }}">{{ $module['title'] }}</label>
                        </div>

                    </div>

                @endif

            @endforeach

        </div>

        <br>

        <h2>Negozi (dove l'utente opera)</h2>
        
        <div class="row">

            @foreach($retails as $retail)

                <div class="col-3">

                    <div class="custom-control custom-switch custom-switch-md form-control-lg">
                        <input type="checkbox"
                               class="custom-control-input"
                               id="retail_{{ $retail->id }}"
                               name="retails[{{ $retail->id }}]"
                               @if(isset($retails_user[$retail->id]) && $retails_user[$retail->id] == 'on')
                               checked
                            @endif>
                        <label class="custom-control-label" for="retail_{{ $retail->id }}">{{ $retail->name }}</label>
                    </div>

                </div>

            @endforeach

        </div>

        <div class="text-right">

            <a href="javascript: history.go(-1);" class="btn btn-secondary">Annulla</a>
            <button type="submit" class="btn btn-success">@if(isset($user->id)) Modifica @else Inserisci @endif</button>

        </div>

    </form>

@endsection
