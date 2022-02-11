<ul class="navbar-nav mr-auto">

    <li class="nav-item {{ Route::is('cash*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('cash') }}">{{ __('Cassa') }}</a>
    </li>

    <li class="nav-item {{ Route::is('store*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('store') }}">{{ __('Magazzino') }}</a>
    </li>

    <li class="nav-item {{ Route::is('customer*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('customer') }}">{{ __('layout.customer.title') }}</a>
    </li>

</ul>
