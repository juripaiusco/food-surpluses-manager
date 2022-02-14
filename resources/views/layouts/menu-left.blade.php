<ul class="navbar-nav mr-auto">

    <li class="nav-item {{ Route::is('cash*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('cash') }}">{{ __('Cassa') }}</a>
    </li>

    <li class="nav-item {{ Route::is('products*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('products') }}">{{ __('Magazzino') }}</a>
    </li>

    <li class="nav-item {{ Route::is('customers*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('customers') }}">{{ __('layout.customers.title') }}</a>
    </li>

</ul>
